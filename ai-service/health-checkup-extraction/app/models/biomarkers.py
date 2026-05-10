"""Canonical biomarker enum + their canonical units.

Why canonical units matter: rules and reference ranges are written *once* against
canonical units. The normalizer's job is to convert anything else into these
before rules run. Never trust the unit on the report — convert and verify.
"""
from __future__ import annotations

from enum import Enum


class Biomarker(str, Enum):
    # --- Glucose / diabetes ---
    GLUCOSE_FASTING = "glucose_fasting"
    GLUCOSE_RANDOM = "glucose_random"
    GLUCOSE_POSTPRANDIAL = "glucose_postprandial"
    HBA1C = "hba1c"

    # --- Lipids ---
    TOTAL_CHOLESTEROL = "total_cholesterol"
    LDL = "ldl"
    HDL = "hdl"
    TRIGLYCERIDES = "triglycerides"
    NON_HDL = "non_hdl"  # derived

    # --- Liver ---
    ALT = "alt"
    AST = "ast"
    ALP = "alp"
    GGT = "ggt"
    BILIRUBIN_TOTAL = "bilirubin_total"
    BILIRUBIN_DIRECT = "bilirubin_direct"
    ALBUMIN = "albumin"

    # --- Kidney ---
    CREATININE = "creatinine"
    BUN = "bun"
    EGFR = "egfr"  # derived if not present
    URIC_ACID = "uric_acid"

    # --- CBC ---
    HEMOGLOBIN = "hemoglobin"
    HEMATOCRIT = "hematocrit"
    RBC = "rbc"
    WBC = "wbc"
    PLATELETS = "platelets"
    MCV = "mcv"

    # --- Thyroid ---
    TSH = "tsh"
    FREE_T4 = "free_t4"
    FREE_T3 = "free_t3"

    # --- Inflammation ---
    CRP = "crp"
    ESR = "esr"

    # --- Electrolytes ---
    SODIUM = "sodium"
    POTASSIUM = "potassium"
    CHLORIDE = "chloride"
    CALCIUM = "calcium"

    # --- Vitamins ---
    VITAMIN_D_25OH = "vitamin_d_25oh"
    VITAMIN_B12 = "vitamin_b12"

    # --- Anthropometric / vitals ---
    BMI = "bmi"
    WAIST_CM = "waist_cm"
    BP_SYSTOLIC = "bp_systolic"
    BP_DIASTOLIC = "bp_diastolic"


# Canonical units. Rules are written against these. The normalizer converts
# anything else to these before evaluation.
CANONICAL_UNIT: dict[Biomarker, str] = {
    Biomarker.GLUCOSE_FASTING: "mg/dL",
    Biomarker.GLUCOSE_RANDOM: "mg/dL",
    Biomarker.GLUCOSE_POSTPRANDIAL: "mg/dL",
    Biomarker.HBA1C: "%",  # NGSP / DCCT-aligned percent
    Biomarker.TOTAL_CHOLESTEROL: "mg/dL",
    Biomarker.LDL: "mg/dL",
    Biomarker.HDL: "mg/dL",
    Biomarker.TRIGLYCERIDES: "mg/dL",
    Biomarker.NON_HDL: "mg/dL",
    Biomarker.ALT: "U/L",
    Biomarker.AST: "U/L",
    Biomarker.ALP: "U/L",
    Biomarker.GGT: "U/L",
    Biomarker.BILIRUBIN_TOTAL: "mg/dL",
    Biomarker.BILIRUBIN_DIRECT: "mg/dL",
    Biomarker.ALBUMIN: "g/dL",
    Biomarker.CREATININE: "mg/dL",
    Biomarker.BUN: "mg/dL",
    Biomarker.EGFR: "mL/min/1.73m2",
    Biomarker.URIC_ACID: "mg/dL",
    Biomarker.HEMOGLOBIN: "g/dL",
    Biomarker.HEMATOCRIT: "%",
    Biomarker.RBC: "10^6/uL",
    Biomarker.WBC: "10^3/uL",
    Biomarker.PLATELETS: "10^3/uL",
    Biomarker.MCV: "fL",
    Biomarker.TSH: "mIU/L",
    Biomarker.FREE_T4: "ng/dL",
    Biomarker.FREE_T3: "pg/mL",
    Biomarker.CRP: "mg/L",
    Biomarker.ESR: "mm/hr",
    Biomarker.SODIUM: "mmol/L",
    Biomarker.POTASSIUM: "mmol/L",
    Biomarker.CHLORIDE: "mmol/L",
    Biomarker.CALCIUM: "mg/dL",
    Biomarker.VITAMIN_D_25OH: "ng/mL",
    Biomarker.VITAMIN_B12: "pg/mL",
    Biomarker.BMI: "kg/m2",
    Biomarker.WAIST_CM: "cm",
    Biomarker.BP_SYSTOLIC: "mmHg",
    Biomarker.BP_DIASTOLIC: "mmHg",
}


# Topic groupings for RAG retrieval — one query per cluster, not one per biomarker.
TOPIC_GROUPS: dict[str, list[Biomarker]] = {
    "glucose": [
        Biomarker.GLUCOSE_FASTING,
        Biomarker.GLUCOSE_RANDOM,
        Biomarker.GLUCOSE_POSTPRANDIAL,
        Biomarker.HBA1C,
    ],
    "lipids": [
        Biomarker.TOTAL_CHOLESTEROL,
        Biomarker.LDL,
        Biomarker.HDL,
        Biomarker.TRIGLYCERIDES,
        Biomarker.NON_HDL,
    ],
    "liver": [
        Biomarker.ALT,
        Biomarker.AST,
        Biomarker.ALP,
        Biomarker.GGT,
        Biomarker.BILIRUBIN_TOTAL,
        Biomarker.BILIRUBIN_DIRECT,
        Biomarker.ALBUMIN,
    ],
    "kidney": [
        Biomarker.CREATININE,
        Biomarker.BUN,
        Biomarker.EGFR,
        Biomarker.URIC_ACID,
    ],
    "cbc": [
        Biomarker.HEMOGLOBIN,
        Biomarker.HEMATOCRIT,
        Biomarker.RBC,
        Biomarker.WBC,
        Biomarker.PLATELETS,
        Biomarker.MCV,
    ],
    "thyroid": [Biomarker.TSH, Biomarker.FREE_T4, Biomarker.FREE_T3],
    "inflammation": [Biomarker.CRP, Biomarker.ESR],
    "electrolytes": [
        Biomarker.SODIUM,
        Biomarker.POTASSIUM,
        Biomarker.CHLORIDE,
        Biomarker.CALCIUM,
    ],
    "vitamins": [Biomarker.VITAMIN_D_25OH, Biomarker.VITAMIN_B12],
    "anthropometric": [
        Biomarker.BMI,
        Biomarker.WAIST_CM,
        Biomarker.BP_SYSTOLIC,
        Biomarker.BP_DIASTOLIC,
    ],
}


def topic_for(biomarker: Biomarker) -> str:
    for topic, members in TOPIC_GROUPS.items():
        if biomarker in members:
            return topic
    return "general"
