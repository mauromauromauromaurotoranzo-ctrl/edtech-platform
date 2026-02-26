'use client'

import { useState } from 'react'
import { 
  Users, BookOpen, GraduationCap, TrendingUp, 
  Plus, Search, MoreVertical, Edit2, Trash2,
  Shield, LogOut, Menu, X
} from 'lucide-react'

// Mock data
const instructors = [
  { id: 1, name: 'Dr. María García', email: 'maria@edtech.com', students: 45, courses: 3, status: 'active', joined: '2024-01-15' },
  { id: 2, name: 'Prof. Carlos López', email: 'carlos@edtech.com', students: 32, courses: 2, status: 'active', joined: '2024-02-20' },
  { id: 3, name: 'Dra. Ana Martínez', email: 'ana@edtech.com', students: 0, courses: 0, status: 'pending', joined: '2024-03-10' },
]

const stats = [
  { label: 'Total Instructores', value: '12', icon: GraduationCap, color: 'blue' },
  { label: 'Estudiantes Activos', value: '348', icon: Users, color: 'green' },
  { label: 'Cursos Publicados', value: '28', icon: BookOpen, color: 'purple' },
  { label: 'Ingresos Mensuales', value: '$12,450', icon: TrendingUp, color: 'amber' },
]

export default function AdminDashboardPage() {
  const [sidebarOpen, setSidebarOpen] = useState(true)
  const [activeTab, setActiveTab] = useState('instructors')
  const [searchQuery, setSearchQuery] = useState('')

  return (
    <div className="min-h-screen bg-slate-50 flex">
      {/* Sidebar */}
      <aside className={`${sidebarOpen ? 'w-64' : 'w-20'} bg-slate-900 text-white transition-all duration-300 flex flex-col`}>
        {/* Logo */}
        <div className="p-6 border-b border-slate-800">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
              <Shield className="w-5 h-5 text-white" />
            </div>
            {sidebarOpen && (
              <div>
                <h1 className="font-bold text-lg">Admin Panel</h1>
                <p className="text-xs text-slate-400">EdTech Platform</p>
              </div>
            )}
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 p-4 space-y-2">
          <NavItem 
            icon={GraduationCap} 
            label="Instructores" 
            active={activeTab === 'instructors'}
            onClick={() => setActiveTab('instructors')}
            collapsed={!sidebarOpen}
          />
          <NavItem 
            icon={Users} 
            label="Estudiantes" 
            active={activeTab === 'students'}
            onClick={() => setActiveTab('students')}
            collapsed={!sidebarOpen}
          />
          <NavItem 
            icon={BookOpen} 
            label="Cursos" 
            active={activeTab === 'courses'}
            onClick={() => setActiveTab('courses')}
            collapsed={!sidebarOpen}
          />
          <NavItem 
            icon={TrendingUp} 
            label="Analytics" 
            active={activeTab === 'analytics'}
            onClick={() => setActiveTab('analytics')}
            collapsed={!sidebarOpen}
          />
        </nav>

        {/* Bottom Actions */}
        <div className="p-4 border-t border-slate-800">
          <button className="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
            <LogOut size={20} />
            {sidebarOpen && <span>Cerrar Sesión</span>}
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 flex flex-col">
        {/* Header */}
        <header className="bg-white border-b px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <button 
              onClick={() => setSidebarOpen(!sidebarOpen)}
              className="p-2 hover:bg-slate-100 rounded-lg"
            >
              {sidebarOpen ? <X size={20} /> : <Menu size={20} />}
            </button>
            <h2 className="text-xl font-semibold text-slate-800">
              Gestión de Instructores
            </h2>
          </div>
          <div className="flex items-center gap-4">
            <button className="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
              <Plus size={18} />
              <span>Nuevo Instructor</span>
            </button>
          </div>
        </header>

        {/* Stats Cards */}
        <div className="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {stats.map((stat, idx) => (
            <StatCard key={idx} {...stat} />
          ))}
        </div>

        {/* Instructors Table */}
        <div className="flex-1 px-6 pb-6">
          <div className="bg-white rounded-xl shadow-sm border">
            {/* Toolbar */}
            <div className="p-4 border-b flex items-center justify-between">
              <div className="relative w-96">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                <input
                  type="text"
                  placeholder="Buscar instructores..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                />
              </div>
              <div className="flex gap-2">
                <select className="px-3 py-2 border rounded-lg text-sm">
                  <option>Todos los estados</option>
                  <option>Activos</option>
                  <option>Pendientes</option>
                  <option>Suspendidos</option>
                </select>
              </div>
            </div>

            {/* Table */}
            <table className="w-full">
              <thead className="bg-slate-50 border-b">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Instructor</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estudiantes</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cursos</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Registro</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y">
                {instructors.map((instructor) => (
                  <tr key={instructor.id} className="hover:bg-slate-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                          {instructor.name.charAt(0)}
                        </div>
                        <div>
                          <p className="font-medium text-slate-900">{instructor.name}</p>
                          <p className="text-sm text-slate-500">{instructor.email}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-slate-600">{instructor.students}</td>
                    <td className="px-6 py-4 text-slate-600">{instructor.courses}</td>
                    <td className="px-6 py-4">
                      <StatusBadge status={instructor.status} />
                    </td>
                    <td className="px-6 py-4 text-slate-600">{instructor.joined}</td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button className="p-2 hover:bg-slate-100 rounded-lg text-slate-600">
                          <Edit2 size={16} />
                        </button>
                        <button className="p-2 hover:bg-red-50 rounded-lg text-red-600">
                          <Trash2 size={16} />
                        </button>
                        <button className="p-2 hover:bg-slate-100 rounded-lg text-slate-600">
                          <MoreVertical size={16} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  )
}

function NavItem({ icon: Icon, label, active, onClick, collapsed }: any) {
  return (
    <button
      onClick={onClick}
      className={`flex items-center gap-3 w-full p-3 rounded-lg transition-colors ${
        active 
          ? 'bg-purple-600 text-white' 
          : 'text-slate-400 hover:bg-slate-800 hover:text-white'
      }`}
    >
      <Icon size={20} />
      {!collapsed && <span>{label}</span>}
    </button>
  )
}

function StatCard({ label, value, icon: Icon, color }: any) {
  const colors: any = {
    blue: 'from-blue-500 to-blue-600',
    green: 'from-green-500 to-green-600',
    purple: 'from-purple-500 to-purple-600',
    amber: 'from-amber-500 to-amber-600',
  }

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm border">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm text-slate-500 mb-1">{label}</p>
          <p className="text-2xl font-bold text-slate-900">{value}</p>
        </div>
        <div className={`w-12 h-12 bg-gradient-to-br ${colors[color]} rounded-xl flex items-center justify-center`}>
          <Icon className="w-6 h-6 text-white" />
        </div>
      </div>
    </div>
  )
}

function StatusBadge({ status }: { status: string }) {
  const styles: any = {
    active: 'bg-green-100 text-green-700',
    pending: 'bg-amber-100 text-amber-700',
    suspended: 'bg-red-100 text-red-700',
  }

  const labels: any = {
    active: 'Activo',
    pending: 'Pendiente',
    suspended: 'Suspendido',
  }

  return (
    <span className={`px-3 py-1 rounded-full text-xs font-medium ${styles[status]}`}>
      {labels[status]}
    </span>
  )
}
