'use client'

import { useState } from 'react'
import { 
  BookOpen, MessageSquare, Settings, BarChart3, Upload, Mic,
  Brain, Sparkles, Volume2, Users, Clock, ChevronRight,
  FileText, Video, Headphones, MoreHorizontal, Plus
} from 'lucide-react'

export default function InstructorDashboardPage() {
  const [activeTab, setActiveTab] = useState('content')

  return (
    <div className="min-h-screen bg-slate-50">
      {/* Top Navigation */}
      <header className="bg-white border-b sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-8">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center">
                <Sparkles className="w-5 h-5 text-white" />
              </div>
              <span className="font-bold text-xl text-slate-800">Mi Espacio</span>
            </div>
            <nav className="hidden md:flex items-center gap-1">
              <NavButton active={activeTab === 'content'} onClick={() => setActiveTab('content')}>
                Contenido
              </NavButton>
              <NavButton active={activeTab === 'ai'} onClick={() => setActiveTab('ai')}>
                Configurar IA
              </NavButton>
              <NavButton active={activeTab === 'students'} onClick={() => setActiveTab('students')}>
                Estudiantes
              </NavButton>
              <NavButton active={activeTab === 'analytics'} onClick={() => setActiveTab('analytics')}>
                Analytics
              </NavButton>
            </nav>
          </div>
          <div className="flex items-center gap-4">
            <button className="p-2 hover:bg-slate-100 rounded-lg">
              <Settings size={20} className="text-slate-600" />
            </button>
            <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-medium">
              M
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-6 py-8">
        {activeTab === 'content' && <ContentTab />}
        {activeTab === 'ai' && <AIConfigTab />}
        {activeTab === 'students' && <StudentsTab />}
        {activeTab === 'analytics' && <AnalyticsTab />}
      </main>
    </div>
  )
}

function NavButton({ children, active, onClick }: any) {
  return (
    <button
      onClick={onClick}
      className={`px-4 py-2 rounded-lg font-medium transition-colors ${
        active 
          ? 'bg-purple-100 text-purple-700' 
          : 'text-slate-600 hover:bg-slate-100'
      }`}
    >
      {children}
    </button>
  )
}

// CONTENT TAB
function ContentTab() {
  return (
    <div className="space-y-8">
      {/* Welcome Section */}
      <div className="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white">
        <h1 className="text-3xl font-bold mb-2">¡Bienvenido, Profesor!</h1>
        <p className="text-purple-100 mb-6">Gestiona tu contenido y crea experiencias de aprendizaje únicas con IA.</p>
        <div className="flex gap-4">
          <button className="px-6 py-3 bg-white text-purple-600 rounded-xl font-medium hover:bg-purple-50 transition-colors flex items-center gap-2">
            <Plus size={18} />
            Nuevo Curso
          </button>
          <button className="px-6 py-3 bg-white/20 text-white rounded-xl font-medium hover:bg-white/30 transition-colors flex items-center gap-2">
            <Upload size={18} />
            Subir Material
          </button>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <QuickStat icon={BookOpen} label="Cursos Activos" value="5" color="blue" />
        <QuickStat icon={Users} label="Estudiantes" value="127" color="green" />
        <QuickStat icon={Headphones} label="Clones de Voz" value="2" color="purple" />
        <QuickStat icon={MessageSquare} label="Conversaciones" value="1,234" color="amber" />
      </div>

      {/* Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Knowledge Bases */}
        <div className="lg:col-span-2 space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-bold text-slate-800">Mis Bases de Conocimiento</h2>
            <button className="text-purple-600 font-medium hover:underline">Ver todos</button>
          </div>
          
          <div className="grid gap-4">
            <KnowledgeBaseCard 
              title="Física Cuántica Avanzada"
              description="Material completo sobre mecánica cuántica, desde principios básicos hasta aplicaciones modernas."
              files={12}
              students={45}
              lastUpdated="Hace 2 días"
              status="active"
            />
            <KnowledgeBaseCard 
              title="Programación Python"
              description="Curso introductorio a Python con ejercicios prácticos y proyectos reales."
              files={8}
              students={82}
              lastUpdated="Hace 5 horas"
              status="active"
            />
          </div>
        </div>

        {/* Voice Cloning Section */}
        <div className="space-y-6">
          <h2 className="text-xl font-bold text-slate-800">Mis Voces</h2>
          
          <div className="bg-white rounded-xl shadow-sm border p-6">
            <div className="flex items-center gap-4 mb-4">
              <div className="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                <Volume2 className="w-8 h-8 text-white" />
              </div>
              <div>
                <h3 className="font-semibold text-slate-800">Voz Principal</h3>
                <p className="text-sm text-slate-500">Entrenada hace 3 días</p>
              </div>
            </div>
            <div className="flex gap-2">
              <button className="flex-1 py-2 bg-purple-100 text-purple-700 rounded-lg font-medium hover:bg-purple-200 transition-colors">
                Probar
              </button>
              <button className="flex-1 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-colors">
                Editar
              </button>
            </div>
          </div>

          <button className="w-full py-4 border-2 border-dashed border-slate-300 rounded-xl text-slate-500 hover:border-purple-500 hover:text-purple-600 transition-colors flex flex-col items-center gap-2">
            <Mic size={24} />
            <span className="font-medium">Grabar Nueva Voz</span>
          </button>
        </div>
      </div>
    </div>
  )
}

// AI CONFIGURATION TAB
function AIConfigTab() {
  const [attitude, setAttitude] = useState(50)
  const [formality, setFormality] = useState(50)
  const [detailLevel, setDetailLevel] = useState(50)

  return (
    <div className="max-w-4xl mx-auto space-y-8">
      <div>
        <h1 className="text-3xl font-bold text-slate-800 mb-2">Configurar tu IA Personal</h1>
        <p className="text-slate-600">Define cómo tu asistente de IA interactuará con tus estudiantes.</p>
      </div>

      {/* Personality Section */}
      <section className="bg-white rounded-xl shadow-sm border p-8">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
            <Brain className="w-5 h-5 text-white" />
          </div>
          <div>
            <h2 className="text-xl font-bold text-slate-800">Personalidad (Actitud)</h2>
            <p className="text-sm text-slate-500">Define el "alma" de tu asistente</p>
          </div>
        </div>

        <div className="space-y-6">
          {/* Attitude Slider */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <label className="font-medium text-slate-700">Energía / Entusiasmo</label>
              <span className="text-sm text-slate-500">
                {attitude < 33 ? '😌 Tranquilo' : attitude < 66 ? '🙂 Equilibrado' : '⚡ Enérgico'}
              </span>
            </div>
            <input
              type="range"
              min="0"
              max="100"
              value={attitude}
              onChange={(e) => setAttitude(Number(e.target.value))}
              className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
            />
            <div className="flex justify-between text-xs text-slate-400 mt-1">
              <span>Tranquilo y sereno</span>
              <span>Moderado</span>
              <span>Enérgico y motivador</span>
            </div>
          </div>

          {/* Personality Traits */}
          <div className="grid grid-cols-2 gap-4">
            <TraitCard 
              title="Motivador"
              description="Celebra logros y fomenta el progreso"
              selected={true}
            />
            <TraitCard 
              title="Paciente"
              description="Explica una y otra vez sin frustrarse"
              selected={true}
            />
            <TraitCard 
              title="Desafiante"
              description="Empuja a los estudiantes fuera de su zona de confort"
              selected={false}
            />
            <TraitCard 
              title="Empático"
              description="Reconoce emociones y adapta el tono"
              selected={true}
            />
          </div>
        </div>
      </section>

      {/* Dialogue Style Section */}
      <section className="bg-white rounded-xl shadow-sm border p-8">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
            <MessageSquare className="w-5 h-5 text-white" />
          </div>
          <div>
            <h2 className="text-xl font-bold text-slate-800">Estilo de Diálogo</h2>
            <p className="text-sm text-slate-500">Cómo se comunica tu asistente</p>
          </div>
        </div>

        <div className="space-y-6">
          {/* Formality */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <label className="font-medium text-slate-700">Formalidad</label>
              <span className="text-sm text-slate-500">
                {formality < 33 ? '😎 Casual' : formality < 66 ? '👔 Semi-formal' : '🎩 Formal'}
              </span>
            </div>
            <input
              type="range"
              min="0"
              max="100"
              value={formality}
              onChange={(e) => setFormality(Number(e.target.value))}
              className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
            />
            <div className="flex justify-between text-xs text-slate-400 mt-1">
              <span>"¡Qué onda! Vamos a aprender"</span>
              <span>"Hola, comencemos"</span>
              <span>"Buenos días, procedamos"</span>
            </div>
          </div>

          {/* Detail Level */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <label className="font-medium text-slate-700">Nivel de Detalle</label>
              <span className="text-sm text-slate-500">
                {detailLevel < 33 ? '⚡ Conciso' : detailLevel < 66 ? '📝 Balanceado' : '📚 Detallado'}
              </span>
            </div>
            <input
              type="range"
              min="0"
              max="100"
              value={detailLevel}
              onChange={(e) => setDetailLevel(Number(e.target.value))}
              className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-green-600"
            />
            <div className="flex justify-between text-xs text-slate-400 mt-1">
              <span>Respuestas cortas y directas</span>
              <span>Balanceado</span>
              <span>Explicaciones profundas</span>
            </div>
          </div>

          {/* Dialogue Examples */}
          <div className="bg-slate-50 rounded-lg p-4">
            <p className="text-sm font-medium text-slate-700 mb-2">Ejemplo de respuesta:</p>
            <p className="text-slate-600 italic">
              "{getDialogueExample(formality, detailLevel)}"
            </p>
          </div>
        </div>
      </section>

      {/* Teaching Methods */}
      <section className="bg-white rounded-xl shadow-sm border p-8">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
            <Sparkles className="w-5 h-5 text-white" />
          </div>
          <div>
            <h2 className="text-xl font-bold text-slate-800">Métodos Pedagógicos</h2>
            <p className="text-sm text-slate-500">Técnicas de enseñanza preferidas</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <MethodCard 
            title="Aprendizaje por Descubrimiento"
            description="Guía a estudiantes mediante preguntas para que lleguen a sus propias conclusiones"
            icon="🔍"
            selected={true}
          />
          <MethodCard 
            title="Ejemplos Prácticos"
            description="Usa casos reales y aplicaciones del mundo real"
            icon="🛠️"
            selected={true}
          />
          <MethodCard 
            title="Analogías Cotidianas"
            description="Explica conceptos complejos con situaciones familiares"
            icon="🎯"
            selected={false}
          />
          <MethodCard 
            title="Visualización Mental"
            description="Crea imágenes mentales para facilitar comprensión"
            icon="🎨"
            selected={true}
          />
          <MethodCard 
            title="Práctica Espaciada"
            description="Repite conceptos en intervalos óptimos para retención"
            icon="⏰"
            selected={true}
          />
          <MethodCard 
            title="Gamificación"
            description="Usa elementos de juego para motivar"
            icon="🎮"
            selected={false}
          />
        </div>
      </section>

      {/* Save Button */}
      <div className="flex justify-end gap-4">
        <button className="px-6 py-3 border border-slate-300 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition-colors">
          Vista Previa
        </button>
        <button className="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-medium hover:from-purple-700 hover:to-blue-700 transition-colors">
          Guardar Configuración
        </button>
      </div>
    </div>
  )
}

// Helper functions and components...
function getDialogueExample(formality: number, detail: number) {
  if (formality < 33 && detail < 33) return "¡Fácil! Es como subir una escalera, paso a paso."
  if (formality < 33 && detail > 66) return "Mira, te explico todo paso a paso con ejemplos súper claros..."
  if (formality > 66 && detail < 33) return "Procedamos con el análisis sistemático."
  if (formality > 66 && detail > 66) return "Permítame elaborar una explicación comprehensiva..."
  return "Veamos esto con calma, te explico los pasos principales."
}

function QuickStat({ icon: Icon, label, value, color }: any) {
  const colors: any = {
    blue: 'bg-blue-100 text-blue-600',
    green: 'bg-green-100 text-green-600',
    purple: 'bg-purple-100 text-purple-600',
    amber: 'bg-amber-100 text-amber-600',
  }

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm border flex items-center gap-4">
      <div className={`w-12 h-12 ${colors[color]} rounded-xl flex items-center justify-center`}>
        <Icon size={24} />
      </div>
      <div>
        <p className="text-2xl font-bold text-slate-800">{value}</p>
        <p className="text-sm text-slate-500">{label}</p>
      </div>
    </div>
  )
}

function KnowledgeBaseCard({ title, description, files, students, lastUpdated, status }: any) {
  return (
    <div className="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow">
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center gap-3">
          <div className="w-12 h-12 bg-gradient-to-br from-purple-100 to-blue-100 rounded-xl flex items-center justify-center">
            <BookOpen className="w-6 h-6 text-purple-600" />
          </div>
          <div>
            <h3 className="font-semibold text-slate-800">{title}</h3>
            <p className="text-sm text-slate-500">{lastUpdated}</p>
          </div>
        </div>
        <span className={`px-3 py-1 rounded-full text-xs font-medium ${
          status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'
        }`}>
          {status === 'active' ? 'Activo' : 'Borrador'}
        </span>
      </div>
      <p className="text-slate-600 text-sm mb-4 line-clamp-2">{description}</p>
      <div className="flex items-center gap-6 text-sm text-slate-500">
        <span className="flex items-center gap-1">
          <FileText size={16} />
          {files} archivos
        </span>
        <span className="flex items-center gap-1">
          <Users size={16} />
          {students} estudiantes
        </span>
      </div>
    </div>
  )
}

function TraitCard({ title, description, selected }: any) {
  return (
    <button className={`p-4 rounded-xl border-2 text-left transition-all ${
      selected 
        ? 'border-purple-500 bg-purple-50' 
        : 'border-slate-200 hover:border-purple-300'
    }`}>
      <h4 className={`font-semibold mb-1 ${selected ? 'text-purple-900' : 'text-slate-800'}`}>
        {title}
      </h4>
      <p className={`text-sm ${selected ? 'text-purple-700' : 'text-slate-500'}`}>
        {description}
      </p>
    </button>
  )
}

function MethodCard({ title, description, icon, selected }: any) {
  return (
    <button className={`p-4 rounded-xl border-2 text-left transition-all ${
      selected 
        ? 'border-amber-500 bg-amber-50' 
        : 'border-slate-200 hover:border-amber-300'
    }`}>
      <div className="text-2xl mb-2">{icon}</div>
      <h4 className={`font-semibold mb-1 ${selected ? 'text-amber-900' : 'text-slate-800'}`}>
        {title}
      </h4>
      <p className={`text-sm ${selected ? 'text-amber-700' : 'text-slate-500'}`}>
        {description}
      </p>
    </button>
  )
}

// Placeholder tabs
function StudentsTab() {
  return (
    <div className="text-center py-20">
      <Users size={64} className="mx-auto text-slate-300 mb-4" />
      <h2 className="text-xl font-semibold text-slate-700">Gestión de Estudiantes</h2>
      <p className="text-slate-500">Próximamente...</p>
    </div>
  )
}

function AnalyticsTab() {
  return (
    <div className="text-center py-20">
      <BarChart3 size={64} className="mx-auto text-slate-300 mb-4" />
      <h2 className="text-xl font-semibold text-slate-700">Analytics Dashboard</h2>
      <p className="text-slate-500">Próximamente...</p>
    </div>
  )
}
