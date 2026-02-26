'use client'

import { useState, useRef, useCallback } from 'react'
import { 
  Upload, Mic, StopCircle, Play, Pause, Trash2, FileText,
  Video, Headphones, CheckCircle, XCircle, Loader2, X
} from 'lucide-react'

interface FileUpload {
  id: string
  name: string
  type: 'pdf' | 'video' | 'audio' | 'doc'
  size: string
  progress: number
  status: 'uploading' | 'completed' | 'error'
}

interface VoiceSample {
  id: string
  name: string
  duration: string
  waveform: number[]
}

export default function UploadContentPage() {
  const [activeTab, setActiveTab] = useState('files')
  const [files, setFiles] = useState<FileUpload[]>([])
  const [voiceSamples, setVoiceSamples] = useState<VoiceSample[]>([])
  const [isRecording, setIsRecording] = useState(false)
  const [recordingTime, setRecordingTime] = useState(0)
  const [dragActive, setDragActive] = useState(false)
  
  const fileInputRef = useRef<HTMLInputElement>(null)
  const mediaRecorderRef = useRef<MediaRecorder | null>(null)
  const chunksRef = useRef<Blob[]>([])
  const timerRef = useRef<NodeJS.Timeout | null>(null)

  // Drag and drop handlers
  const handleDrag = useCallback((e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true)
    } else if (e.type === 'dragleave') {
      setDragActive(false)
    }
  }, [])

  const handleDrop = useCallback((e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setDragActive(false)
    
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFiles(e.dataTransfer.files)
    }
  }, [])

  const handleFiles = (fileList: FileList) => {
    Array.from(fileList).forEach((file) => {
      const newFile: FileUpload = {
        id: Math.random().toString(36).substr(2, 9),
        name: file.name,
        type: getFileType(file.type),
        size: formatFileSize(file.size),
        progress: 0,
        status: 'uploading',
      }
      
      setFiles(prev => [...prev, newFile])
      
      // Simulate upload progress
      simulateUpload(newFile.id)
    })
  }

  const simulateUpload = (fileId: string) => {
    let progress = 0
    const interval = setInterval(() => {
      progress += Math.random() * 15
      if (progress >= 100) {
        progress = 100
        clearInterval(interval)
        setFiles(prev => prev.map(f => 
          f.id === fileId ? { ...f, progress: 100, status: 'completed' } : f
        ))
      } else {
        setFiles(prev => prev.map(f => 
          f.id === fileId ? { ...f, progress } : f
        ))
      }
    }, 500)
  }

  const getFileType = (mimeType: string): 'pdf' | 'video' | 'audio' | 'doc' => {
    if (mimeType.includes('pdf')) return 'pdf'
    if (mimeType.includes('video')) return 'video'
    if (mimeType.includes('audio')) return 'audio'
    return 'doc'
  }

  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  const removeFile = (id: string) => {
    setFiles(prev => prev.filter(f => f.id !== id))
  }

  // Voice recording
  const startRecording = async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true })
      const mediaRecorder = new MediaRecorder(stream)
      mediaRecorderRef.current = mediaRecorder
      chunksRef.current = []
      
      mediaRecorder.ondataavailable = (e) => {
        if (e.data.size > 0) {
          chunksRef.current.push(e.data)
        }
      }
      
      mediaRecorder.onstop = () => {
        const blob = new Blob(chunksRef.current, { type: 'audio/webm' })
        const duration = formatTime(recordingTime)
        
        const newSample: VoiceSample = {
          id: Math.random().toString(36).substr(2, 9),
          name: `Grabación ${voiceSamples.length + 1}`,
          duration,
          waveform: generateWaveform(),
        }
        
        setVoiceSamples(prev => [...prev, newSample])
        setRecordingTime(0)
      }
      
      mediaRecorder.start()
      setIsRecording(true)
      
      // Start timer
      timerRef.current = setInterval(() => {
        setRecordingTime(prev => prev + 1)
      }, 1000)
      
    } catch (err) {
      console.error('Error accessing microphone:', err)
      alert('No se pudo acceder al micrófono. Por favor verifica los permisos.')
    }
  }

  const stopRecording = () => {
    if (mediaRecorderRef.current && isRecording) {
      mediaRecorderRef.current.stop()
      mediaRecorderRef.current.stream.getTracks().forEach(track => track.stop())
      setIsRecording(false)
      
      if (timerRef.current) {
        clearInterval(timerRef.current)
      }
    }
  }

  const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
  }

  const generateWaveform = (): number[] => {
    return Array.from({ length: 50 }, () => Math.random() * 100)
  }

  const removeVoiceSample = (id: string) => {
    setVoiceSamples(prev => prev.filter(s => s.id !== id))
  }

  return (
    <div className="min-h-screen bg-slate-50">
      {/* Header */}
      <header className="bg-white border-b sticky top-0 z-50">
        <div className="max-w-6xl mx-auto px-6 py-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-slate-800">Subir Contenido</h1>
              <p className="text-slate-500">Agrega materiales y crea tu voz AI</p>
            </div>
            <a href="/instructor/dashboard" className="text-slate-600 hover:text-slate-800">
              ← Volver al Dashboard
            </a>
          </div>
        </div>
      </header>

      <main className="max-w-6xl mx-auto px-6 py-8">
        {/* Tabs */}
        <div className="flex gap-2 mb-8">
          <TabButton 
            active={activeTab === 'files'} 
            onClick={() => setActiveTab('files')}
            icon={FileText}
          >
            Archivos
          </TabButton>
          <TabButton 
            active={activeTab === 'voice'} 
            onClick={() => setActiveTab('voice')}
            icon={Mic}
          >
            Clonar Voz
          </TabButton>
        </div>

        {/* Files Tab */}
        {activeTab === 'files' && (
          <div className="space-y-6">
            {/* Upload Area */}
            <div
              className={`border-2 border-dashed rounded-2xl p-12 text-center transition-colors ${
                dragActive 
                  ? 'border-purple-500 bg-purple-50' 
                  : 'border-slate-300 hover:border-purple-400'
              }`}
              onDragEnter={handleDrag}
              onDragLeave={handleDrag}
              onDragOver={handleDrag}
              onDrop={handleDrop}
            >
              <div className="w-20 h-20 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <Upload className="w-10 h-10 text-purple-600" />
              </div>
              
              <h3 className="text-lg font-semibold text-slate-800 mb-2">
                Arrastra archivos aquí
              </h3>
              <p className="text-slate-500 mb-4">
                o haz clic para seleccionar archivos
              </p>
              
              <input
                ref={fileInputRef}
                type="file"
                multiple
                className="hidden"
                onChange={(e) => e.target.files && handleFiles(e.target.files)}
                accept=".pdf,.doc,.docx,.mp4,.mp3,.wav"
              />
              
              <button
                onClick={() => fileInputRef.current?.click()}
                className="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition-colors"
              >
                Seleccionar Archivos
              </button>
              
              <p className="text-sm text-slate-400 mt-4">
                PDF, Word, Video, Audio (máx. 100MB)
              </p>
            </div>

            {/* File List */}
            {files.length > 0 && (
              <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div className="px-6 py-4 border-b bg-slate-50">
                  <h3 className="font-semibold text-slate-800">
                    Archivos ({files.length})
                  </h3>
                </div>
                
                <div className="divide-y">
                  {files.map((file) => (
                    <div key={file.id} className="p-6 flex items-center gap-4">
                      <FileIcon type={file.type} />
                      
                      <div className="flex-1">
                        <div className="flex items-center justify-between mb-2">
                          <p className="font-medium text-slate-800">{file.name}</p>
                          <span className="text-sm text-slate-500">{file.size}</span>
                        </div>
                        
                        {/* Progress Bar */}
                        <div className="flex items-center gap-3">
                          <div className="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div 
                              className={`h-full rounded-full transition-all ${
                                file.status === 'completed' 
                                  ? 'bg-green-500' 
                                  : 'bg-purple-500'
                              }`}
                              style={{ width: `${file.progress}%` }}
                            />
                          </div>
                          
                          {file.status === 'uploading' && (
                            <Loader2 className="w-5 h-5 text-purple-600 animate-spin" />
                          )}
                          
                          {file.status === 'completed' && (
                            <CheckCircle className="w-5 h-5 text-green-500" />
                          )}
                          
                          <button 
                            onClick={() => removeFile(file.id)}
                            className="p-1 hover:bg-red-50 rounded text-slate-400 hover:text-red-500"
                          >
                            <X size={18} />
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}

        {/* Voice Tab */}
        {activeTab === 'voice' && (
          <div className="space-y-8">
            {/* Recording Section */}
            <div className="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl p-8 text-white">
              <div className="text-center">
                <h2 className="text-2xl font-bold mb-2">Graba tu Voz</h2>
                <p className="text-purple-100 mb-8">
                  Graba muestras de tu voz para crear tu clon AI. <br/>
                  Lee un texto natural durante 30-60 segundos.
                </p>

                {/* Recording Timer */}
                <div className="mb-8">
                  <div className={`text-6xl font-mono font-bold mb-4 ${
                    isRecording ? 'animate-pulse' : ''
                  }`}>
                    {formatTime(recordingTime)}
                  </div>
                  
                  {isRecording && (
                    <div className="flex items-center justify-center gap-2 text-red-300">
                      <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse" />
                      Grabando...
                    </div>
                  )}
                </div>

                {/* Record Button */}
                <button
                  onClick={isRecording ? stopRecording : startRecording}
                  className={`w-24 h-24 rounded-full flex items-center justify-center mx-auto transition-all ${
                    isRecording 
                      ? 'bg-red-500 hover:bg-red-600 animate-pulse' 
                      : 'bg-white text-purple-600 hover:scale-105'
                  }`}
                >
                  {isRecording ? (
                    <StopCircle size={40} />
                  ) : (
                    <Mic size={40} />
                  )}
                </button>

                <p className="mt-6 text-sm text-purple-100">
                  {isRecording 
                    ? 'Haz clic para detener la grabación' 
                    : 'Haz clic para comenzar a grabar'}
                </p>
              </div>
            </div>

            {/* Sample Text */}
            <div className="bg-white rounded-xl shadow-sm border p-6">
              <h3 className="font-semibold text-slate-800 mb-4">Texto de ejemplo para leer:</h3>
              <div className="bg-slate-50 rounded-lg p-4 text-slate-600 leading-relaxed">
                "El aprendizaje es un viaje fascinante que nunca termina. Cada día nos presenta 
                nuevas oportunidades para descubrir, crecer y expandir nuestros horizontes. 
                Como instructor, mi objetivo es guiarte en este camino, ayudándote a conectar 
                los conceptos con tu experiencia diaria y fomentando tu curiosidad natural. 
                Recuerda: no hay preguntas tontas, solo oportunidades para aprender juntos."
              </div>
            </div>

            {/* Voice Samples List */}
            {voiceSamples.length > 0 && (
              <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div className="px-6 py-4 border-b bg-slate-50 flex items-center justify-between">
                  <h3 className="font-semibold text-slate-800">
                    Muestras de Voz ({voiceSamples.length})
                  </h3>
                  
                  <button className="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">
                    Entrenar IA
                  </button>
                </div>
                
                <div className="divide-y">
                  {voiceSamples.map((sample) => (
                    <div key={sample.id} className="p-6">
                      <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center gap-3">
                          <div className="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <Headphones className="w-6 h-6 text-purple-600" />
                          </div>
                          <div>
                            <p className="font-medium text-slate-800">{sample.name}</p>
                            <p className="text-sm text-slate-500">{sample.duration}</p>
                          </div>
                        </div>
                        
                        <div className="flex items-center gap-2">
                          <button className="p-2 hover:bg-slate-100 rounded-lg text-slate-600">
                            <Play size={18} />
                          </button>
                          <button 
                            onClick={() => removeVoiceSample(sample.id)}
                            className="p-2 hover:bg-red-50 rounded-lg text-slate-400 hover:text-red-500"
                          >
                            <Trash2 size={18} />
                          </button>
                        </div>
                      </div>
                      
                      {/* Waveform Visualization */}
                      <div className="flex items-end gap-1 h-12">
                        {sample.waveform.map((height, idx) => (
                          <div
                            key={idx}
                            className="flex-1 bg-purple-400 rounded-t"
                            style={{ height: `${height}%` }}
                          />
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </main>
    </div>
  )
}

function TabButton({ children, active, onClick, icon: Icon }: any) {
  return (
    <button
      onClick={onClick}
      className={`flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-colors ${
        active 
          ? 'bg-purple-600 text-white' 
          : 'bg-white text-slate-600 hover:bg-slate-100 border'
      }`}
    >
      <Icon size={18} />
      {children}
    </button>
  )
}

function FileIcon({ type }: { type: string }) {
  const icons = {
    pdf: { icon: FileText, color: 'bg-red-100 text-red-600' },
    video: { icon: Video, color: 'bg-blue-100 text-blue-600' },
    audio: { icon: Headphones, color: 'bg-amber-100 text-amber-600' },
    doc: { icon: FileText, color: 'bg-blue-100 text-blue-600' },
  }
  
  const { icon: Icon, color } = icons[type as keyof typeof icons] || icons.doc
  
  return (
    <div className={`w-12 h-12 ${color} rounded-xl flex items-center justify-center`}>
      <Icon size={24} />
    </div>
  )
}
