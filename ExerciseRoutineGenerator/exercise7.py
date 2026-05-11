import json
import requests
import os
import chromadb
import numpy as np

# CONFIG
EXERCISE_COLLECTION = "exercise_data1"
PDF_COLLECTION = "pdf_books"
CHROMA_DB_PATH = "/home/alvin/testqdrant/.chromadb"

OLLAMA_CHAT_URL = "http://192.168.111.132:11434/api/generate"
OLLAMA_CHAT2_URL = "http://localhost:11434/api/generate"
OLLAMA_EMBED_URL = "http://192.168.111.132:11434/api/embeddings"

LLM_MODEL = "gemma4:26b"
LLM_MODEL_SMALL = "gemma4:e2b"
EMBED_MODEL = "embeddinggemma"

TOP_K_EXERCISE = 20
TOP_K_PDF = 5

CHAT_HISTORY_PATH = "/home/alvin/testqdrant/chat_memory.json"
PROFILE_PATH = "/home/alvin/testqdrant/user_profile.json"
PROMPT_LOG_PATH = "/home/alvin/testqdrant/prompt_debug.jsonl"

MAX_TURNS = 5
ROUTINE_OUTPUT_PATH = "/home/alvin/testqdrant/weekly_routine.json"

VALID_EQUIPMENT = [
    "assisted",
    "band",
    "barbell",
    "body weight",
    "bosu ball",
    "cable",
    "dumbbell",
    "elliptical machine",
    "ez barbell",
    "hammer",
    "kettlebell",
    "leverage machine",
    "medicine ball",
    "olympic barbell",
    "pull-up bar",
    "resistance band",
    "roller",
    "rope",
    "skierg machine",
    "sled machine",
    "smith machine",
    "stability ball",
    "stationary bike",
    "stepmill machine",
    "tire",
    "trap bar",
    "upper body ergometer",
    "weighted",
    "wheel roller"
]

# CHROMADB SETUP
chroma_client = chromadb.PersistentClient(path=CHROMA_DB_PATH)

exercise_collection = chroma_client.get_or_create_collection(
    name=EXERCISE_COLLECTION
)

pdf_collection = chroma_client.get_or_create_collection(
    name=PDF_COLLECTION
)

# DEBUG LOGGING
def SavePromptRecord(stage: str, prompt_text: str):
    record = {
        "stage": stage,
        "prompt_len": len(prompt_text),
        "prompt": prompt_text
    }
    with open(PROMPT_LOG_PATH, "a", encoding="utf-8") as f:
        f.write(json.dumps(record, ensure_ascii=False) + "\n")

# OLLAMA
def Embed(text):
    response = requests.post(
        OLLAMA_EMBED_URL,
        json={"model": EMBED_MODEL, "prompt": text}
    )

    body = response.json()

    if response.status_code != 200:
        raise RuntimeError(f"Embedding API error: {body}")

    if "embedding" not in body:
        raise RuntimeError(f"Invalid embedding response: {body}")

    return body["embedding"]

def Generate(prompt):
    response = requests.post(
        OLLAMA_CHAT_URL,
        json={
            "model": LLM_MODEL,
            "prompt": prompt,
            "stream": False
        }
    )
    print(response)
    return response.json()["response"]

def GenerateSmall(prompt):
    response = requests.post(
        OLLAMA_CHAT2_URL,
        json={
            "model": LLM_MODEL_SMALL,
            "prompt": prompt,
            "stream": False
        }
    )
    print(response)
    return response.json()["response"]

# MEMORY
def LoadHistory():
    if not os.path.exists(CHAT_HISTORY_PATH):
        return []
    with open(CHAT_HISTORY_PATH, "r", encoding="utf-8") as f:
        return json.load(f)

def SaveHistory(history):
    history = history[-MAX_TURNS:]
    with open(CHAT_HISTORY_PATH, "w", encoding="utf-8") as f:
        json.dump(history, f, indent=2)

# PROFILE STORAGE
def SaveProfile(profile):
    with open(PROFILE_PATH, "w", encoding="utf-8") as f:
        json.dump(profile, f, indent=2)

def LoadProfile():
    if not os.path.exists(PROFILE_PATH):
        return None
    with open(PROFILE_PATH, "r", encoding="utf-8") as f:
        return json.load(f)

def DecipherEquipment(profile: dict):
    """
    Deciphers user equipment input and directly modifies profile.

    Adds:
    profile["equipment_labels"]

    Example:
    "home gym with dumbbells and bands"
    ->
    ["dumbbell", "band"]
    """

    user_equipment_input = profile.get(
        "equipment_available",
        ""
    )

    prompt = f"""
Your task:
Map the user's available equipment into ONLY the valid equipment labels below.

VALID EQUIPMENT LIST:
{chr(10).join(["- " + e for e in VALID_EQUIPMENT])}

Rules:
- Return ONLY JSON array
- Multiple equipment allowed
- Infer intent/synonyms
- If user says:
    "home gym" -> may include dumbbell, barbell, resistance band
    "nothing" or "no equipment" -> body weight
    "pullup station" -> pull-up bar
    "bands" -> band OR resistance band
- Never invent new labels
- Output must be valid JSON

User Input:
{user_equipment_input}

Example Output:
["dumbbell", "barbell"]
"""

    response = GenerateSmall(prompt)

    try:
        parsed = json.loads(response)

        cleaned = []

        for item in parsed:

            item = str(item).strip().lower()

            if item in VALID_EQUIPMENT:
                cleaned.append(item)

        # fallback
        if not cleaned:
            cleaned = ["body weight"]

        cleaned = list(set(cleaned))

    except Exception:

        cleaned = ["body weight"]

    # -------------------------
    # MODIFY PROFILE DIRECTLY
    # -------------------------
    profile["equipment_labels"] = cleaned

    print("\n[Equipment Labels]")
    print(cleaned)

    return profile

# RETRIEVAL (CHROMADB)
def RetrievePDF(query):
    qv = Embed(query)

    res = pdf_collection.query(
        query_embeddings=[qv],
        n_results=TOP_K_PDF
    )

    docs = res.get("documents", [[]])[0]
    metas = res.get("metadatas", [[]])[0]

    results = []
    for doc, meta in zip(docs, metas):
        page = meta.get("page")
        results.append(f"[Page {page}]\n{doc}")

    return results

def RetrieveExercise(query):
    qv = Embed(query)

    res = exercise_collection.query(
        query_embeddings=[qv],
        n_results=TOP_K_EXERCISE
    )

    docs = res.get("documents", [[]])[0]

    return docs

# USER PROFILE
def CollectUserProfile():
    print("\nPlease enter your profile:\n")

    profile = {
        "body_weight": input("Body weight (60 kg): ").strip(),
        "height": input("Height (170 cm): ").strip(),
        "age": input("Age (30): ").strip(),
        "exercise_preference": input("Exercise preference (strength, cardio, mixed): ").strip(),
        "time_available": input("Time per day (45 minutes): ").strip(),
        "available_days": input("Available days (Mon, Wed, Fri): ").strip(),
        "equipment_available": input("Equipment (dumbbells, barbell, none): ").strip(),
        "fitness_goals": input("Goals (fat loss, muscle gain): ").strip(),
        "body_part_focus": input("Body Part Focus (full body, legs, chest): ").strip()
    }

    # modify profile directly
    profile = DecipherEquipment(profile)

    SaveProfile(profile)

    return profile

# FULL PIPELINE
def BuildExerciseQuery(weekly_structure: str, userProfile: dict):
    return f"""
Find exercises that match the following constraints:

Equipment: {userProfile.get('equipment_available')}
Goal: {userProfile.get('fitness_goals')}
Focus: {userProfile.get('body_part_focus')}
Preference: {userProfile.get('exercise_preference')}

Weekly Plan Context:
{weekly_structure}

Rules:
- PRIORITIZE equipment compatibility
- If equipment is NONE → only bodyweight exercises
- Avoid exercises requiring machines if not available
- Match user's goal and focus

Return exercises relevant to this query.
"""

def WeeklyRoutineQuery(query, userProfile):

    # -------- STAGE 1: HEALTH ASSESSMENT --------
    pdf_chunks = RetrievePDF(str(userProfile) + " " + query)

    prompt1 = f"""
You are a fitness expert.

User Profile:
body_weight: {userProfile.get('body_weight')}
height: {userProfile.get('height')}
age: {userProfile.get('age')}
fitness_goals: {userProfile.get('fitness_goals')}
exercise_preference: {userProfile.get('exercise_preference')}
time_available: {userProfile.get('time_available')}
available_days: {userProfile.get('available_days')}
equipment_available: {userProfile.get('equipment_available')}
body_part_focus: {userProfile.get('body_part_focus')}

Knowledge:
{chr(10).join(pdf_chunks)}

Give only the general assessment of user's health based on their profile and exercise types to avoid/can't perform due to their condition
"""
    SavePromptRecord("health_assessment", prompt1)
    assessment = Generate(prompt1)
    SavePromptRecord("assessment", assessment)

    # -------- STAGE 2: WEEKLY STRUCTURE --------
    prompt2 = f"""
Design a weekly workout plan for this user.

User Profile:
body_weight: {userProfile.get('body_weight')}
height: {userProfile.get('height')}
age: {userProfile.get('age')}
fitness_goals: {userProfile.get('fitness_goals')}
exercise_preference: {userProfile.get('exercise_preference')}
time_available: {userProfile.get('time_available')}
available_days: {userProfile.get('available_days')}
equipment_available: {userProfile.get('equipment_available')}
body_part_focus: {userProfile.get('body_part_focus')}

Expert Notes:
{assessment}

Draft a rough weekly structure which will then be used to plan the complete exercise routine.
The weekly structure should consist of:
- The types of exercises required to do, routine focus, intensity, and total duration of routine PER DAY
- The specific body part and muscle required
- Specify what equipment is available to be used for curating the exercises. equipment_available: {userProfile.get('equipment_available')}
"""
    SavePromptRecord("weekly_structure_prompt", prompt2)
    weekly_structure = Generate(prompt2)
    SavePromptRecord("weekly_structure", weekly_structure)

    # -------- STAGE 3: EXERCISE SELECTION --------
    exercise_query = BuildExerciseQuery(weekly_structure, userProfile)
    SavePromptRecord("exercise_query", exercise_query)
    exercise_chunks = RetrieveExercise(exercise_query)

    prompt3 = f"""
ONLY use exercises from the dataset.

Weekly Plan:
{weekly_structure}

Task:
- Adhere to user's profile and weekly plan to select exercise
- Use ONLY exercises from dataset
- Give out full instructions on how to perform each exercise
- Give instructions for each exercise's reps, sets, and intensity
- Do not repeat the exercise
- Generate output according to output rules below

Output Rules:
- Return ONLY valid JSON
- No markdown
- No explanations
- No comments
- Use the exact schema below
- Always return an array for exercises
- Ensure all fields are strings

JSON Schema:
{{
  "weekly_workout_plan": [
    {{
      "day_label": "string",
      "heading": "string",
      "title": "string",
      "duration": "string",
      "description": "string",
      "exercises": [
        {{
          "exercise_id": "string
          "name": "string",
          "detail": "string",
          "description": "string",
          "duration": "string"
        }}
      ]
    }}
  ]
}}

Requirements:
- Create one object per workout day
- Include all exercises under the correct day
- duration must remain human readable
- Keep descriptions concise but informative

Available Exercises:
{chr(10).join(exercise_chunks)}

User Profile:
body_weight: {userProfile.get('body_weight')}
height: {userProfile.get('height')}
age: {userProfile.get('age')}
fitness_goals: {userProfile.get('fitness_goals')}
exercise_preference: {userProfile.get('exercise_preference')}
time_available: {userProfile.get('time_available')}
available_days: {userProfile.get('available_days')}
equipment_available: {userProfile.get('equipment_available')}
body_part_focus: {userProfile.get('body_part_focus')}
"""
    SavePromptRecord("exercise_selection_prompt", prompt3)
    routine = Generate(prompt3)
    SavePromptRecord("routine", routine)

    return routine

def SaveRoutineToJson(answer):
    """
    Saves generated routine into JSON file.
    """

    try:

        # if answer is already dict
        if isinstance(answer, dict):
            parsed = answer

        # if answer is JSON string
        else:
            parsed = json.loads(answer)

        with open(ROUTINE_OUTPUT_PATH, "w", encoding="utf-8") as f:
            json.dump(
                parsed,
                f,
                indent=2,
                ensure_ascii=False
            )

        print(f"\n[Routine Saved]")
        print(ROUTINE_OUTPUT_PATH)

    except Exception as e:

        print("\n[ERROR] Failed to save routine JSON")
        print(e)

        # optional debug dump
        with open(
            ROUTINE_OUTPUT_PATH + ".txt",
            "w",
            encoding="utf-8"
        ) as f:
            f.write(str(answer))

        print("\nRaw output saved as text instead.")

# CHAT LOOP
def ChatLoop():
    print("\nFitness Assistant Ready. Type 'exit' to quit.\n")

    profile = LoadProfile()
    history = LoadHistory()

    if profile is None:
        profile = CollectUserProfile()

    if not history:
        initialQuery = "Create weekly exercise routine"
        answer = WeeklyRoutineQuery(initialQuery, profile)
        SaveRoutineToJson(answer)

        print("\nInitial Routine:\n")
        print(answer)
        print("\n" + "="*50)

    while True:
        userInput = input("\nYou: ")

        if userInput.lower() in ["exit", "quit"]:
            break

        answer = WeeklyRoutineQuery(userInput, profile)

        print("\nAssistant:\n")
        print(answer)
        print("\n" + "="*50)

# MAIN
if __name__ == "__main__":
    ChatLoop()