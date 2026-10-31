import os
from google import genai
from google.genai import types

# A Gemini API kliens inicializálása (a környezeti változóból olvassa ki a kulcsot)
client = genai.Client(api_key=os.environ.get("GEMINI_API_KEY"))

# Célnyelvek beállítása (Nyelv neve: Kimeneti fájlnév)
TARGET_LANGUAGES = {
    "Hungarian": "README.hu.md",
    # Ide jöhet még több nyelv is:
    # "German": "README.de.md",
    # "Spanish": "README.es.md",
}

SYSTEM_INSTRUCTION = (
    "You are a professional technical translator. Translate the provided Markdown "
    "README file into the target language. Keep all Markdown formatting, links, "
    "badges, code blocks, and HTML elements completely intact. Do not translate "
    "code snippets, variable names, terminal commands, or URLs."
)

def translate_content(content, target_language):
    prompt = f"Target Language: {target_language}\n\nDocument Content:\n{content}"
    
    # A legújabb, kifejezetten gyors és intelligens Gemini 2.5 Flash modellt használjuk
    response = client.models.generate_content(
        model="gemini-2.5-flash",
        contents=prompt,
        config=types.GenerateContentConfig(
            system_instruction=SYSTEM_INSTRUCTION,
            temperature=0.2, # Alacsony hőmérséklet a pontos, precíz fordításhoz
        ),
    )
    return response.text

def main():
    if not os.path.exists("README.md"):
        print("A README.md fájl nem található!")
        return

    with open("README.md", "r", encoding="utf-8") as f:
        original_content = f.read()

    for lang_name, filename in TARGET_LANGUAGES.items():
        print(f"Fordítás Gemini-vel ({lang_name} -> {filename})...")
        translated = translate_content(original_content, lang_name)
        
        with open(filename, "w", encoding="utf-8") as f:
            f.write(translated)
        print(f"Sikeresen elkészült: {filename}")

if __name__ == "__main__":
    main()