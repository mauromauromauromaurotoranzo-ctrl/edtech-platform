export default function Home() {
  return (
    <main className="min-h-screen flex flex-col items-center justify-center p-24">
      <div className="text-center">
        <h1 className="text-4xl font-bold text-gray-900 mb-4">
          EdTech Platform
        </h1>
        <p className="text-lg text-gray-600 mb-8">
          Aprende con IA personalizada
        </p>
        <div className="flex gap-4">
          <a href="/login" className="btn-primary">
            Iniciar Sesión
          </a>
          <a href="/register" className="btn-secondary">
            Registrarse
          </a>
        </div>
      </div>
    </main>
  )
}
