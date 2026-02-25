'use client'

import { useAuth } from '@/hooks/useAuth'
import { useRouter } from 'next/navigation'
import { useEffect } from 'react'

export default function DashboardPage() {
  const { user, loading } = useAuth()
  const router = useRouter()

  useEffect(() => {
    if (!loading && !user) {
      router.push('/login')
    }
  }, [user, loading, router])

  if (loading) return <div className="p-8">Cargando...</div>
  if (!user) return null

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white border-b px-6 py-4">
        <div className="flex justify-between items-center">
          <h1 className="text-xl font-bold">EdTech Platform</h1>
          <div className="flex items-center gap-4">
            <span className="text-gray-600">{user.name}</span>
            <button className="btn-secondary text-sm">Cerrar sesión</button>
          </div>
        </div>
      </nav>

      <main className="max-w-6xl mx-auto p-6">
        <h2 className="text-2xl font-bold mb-6">
          Bienvenido, {user.name}
        </h2>

        {user.role === 'student' ? (
          <StudentDashboard />
        ) : (
          <InstructorDashboard />
        )}
      </main>
    </div>
  )
}

function StudentDashboard() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div className="card">
        <h3 className="font-semibold mb-2">Desafío Diario</h3>
        <p className="text-gray-600 text-sm mb-4">Completa tu desafío de hoy</p>
        <a href="/challenge" className="btn-primary block text-center">
          Ver Desafío
        </a>
      </div>

      <div className="card">
        <h3 className="font-semibold mb-2">Chat con IA</h3>
        <p className="text-gray-600 text-sm mb-4">Aprende conversando</p>
        <a href="/chat" className="btn-primary block text-center">
          Iniciar Chat
        </a>
      </div>

      <div className="card">
        <h3 className="font-semibold mb-2">Mi Progreso</h3>
        <p className="text-gray-600 text-sm mb-4">Racha: 5 días 🔥</p>
        <a href="/progress" className="btn-secondary block text-center">
          Ver Detalles
        </a>
      </div>
    </div>
  )
}

function InstructorDashboard() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div className="card">
        <h3 className="font-semibold mb-2">Mis Cursos</h3>
        <p className="text-gray-600 text-sm mb-4">Gestiona tus cursos</p>
        <a href="/courses" className="btn-primary block text-center">
          Ver Cursos
        </a>
      </div>

      <div className="card">
        <h3 className="font-semibold mb-2">Base de Conocimiento</h3>
        <p className="text-gray-600 text-sm mb-4">Sube contenido</p>
        <a href="/knowledge" className="btn-primary block text-center">
          Gestionar
        </a>
      </div>

      <div className="card">
        <h3 className="font-semibold mb-2">Estadísticas</h3>
        <p className="text-gray-600 text-sm mb-4">Ver progreso de estudiantes</p>
        <a href="/stats" className="btn-secondary block text-center">
          Ver Reportes
        </a>
      </div>
    </div>
  )
}
