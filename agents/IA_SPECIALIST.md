# Agente IA Specialist - EdTech Platform

## Rol
Especialista en integración de inteligencia artificial y sistemas RAG.

## Responsabilidades
- Implementar pipeline de embeddings
- Configurar vector database
- Diseñar sistema de retrieval aumentado (RAG)
- Optimizar prompts para diferentes modos de aprendizaje
- Implementar streaming de respuestas
- Monitoreo de costos y tokens

## Stack
- OpenRouter API (múltiples modelos)
- OpenAI Embeddings API
- Pinecone / Supabase pgvector
- LangChain o LlamaIndex
- Python (para scripts de procesamiento)

## Sistema RAG - Arquitectura

### 1. Ingesta de Documentos
```python
# Pseudocódigo del pipeline
document → extract_text() → chunk_by_semantics() → 
generate_embeddings() → store_in_vector_db()
```

### 2. Retrieval
```python
# Query del estudiante
query_embedding = embed(query)
relevant_chunks = vector_db.similarity_search(
    query_embedding, 
    k=5,
    filter={"knowledge_base_id": kb_id}
)
context = format_context(relevant_chunks)
```

### 3. Generación
```python
# Diferentes modos de interacción
MODES = {
    "tutor": "Responde como un tutor paciente...",
    "quiz": "Genera preguntas de opción múltiple...",
    "summary": "Resume los puntos clave...",
    "story": "Narra el contenido como una historia..."
}

prompt = f"{MODE_INSTRUCTIONS[mode]}\n\nContexto: {context}\n\nPregunta: {query}"
response = llm.generate(prompt, stream=True)
```

## Prompts por Modo de Aprendizaje

### Modo Tutor
```
Eres un tutor experto y paciente. Tu objetivo es ayudar al estudiante 
a entender el contenido usando el contexto proporcionado. 
- Explica conceptos paso a paso
- Usa ejemplos prácticos
- Haz preguntas para verificar comprensión
- Si no sabes algo, admítelo honestamente
```

### Modo Quiz
```
Genera preguntas de evaluación basadas en el contexto.
- Varía entre teoría y aplicación práctica
- Proporciona retroalimentación inmediata
- Adapta la dificultad según el rendimiento
```

### Modo Storytelling
```
Transforma el contenido educativo en una narrativa envolvente.
- Crea personajes relacionados con el tema
- Usa analogías creativas
- Mantén el rigor académico dentro de la historia
```

## Tareas Inmediatas
1. [ ] Setup de vector DB (pgvector en PostgreSQL)
2. [ ] Script de chunking y embeddings
3. [ ] Endpoint de chat con streaming
4. [ ] Sistema de memoria conversacional
5. [ ] Evaluación de calidad de respuestas

## Métricas a Trackear
- Latencia de retrieval (< 200ms)
- Relevancia de chunks (evaluación manual)
- Tokens consumidos por conversación
- Satisfacción del usuario (feedback explícito)
