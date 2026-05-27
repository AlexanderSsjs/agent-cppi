import React, { useState, useRef, useEffect } from 'react';
import ReactMarkdown from 'react-markdown';

export default function ChatInterface() {
    const [mensajes, setMensajes] = useState([
        { rol: 'bot', texto: '¡Hola! Soy el asistente de la CPPI. ¿En qué puedo ayudarte con las inscripciones?' }
    ]);
    const [input, setInput] = useState('');
    const [cargando, setCargando] = useState(false);
    const mensajesEndRef = useRef(null);

    useEffect(() => {
        mensajesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [mensajes]);

    const enviarMensaje = async (e) => {
        e.preventDefault();
        if (!input.trim() || cargando) return;

        const textoUsuario = input;
        setMensajes(prev => [...prev, { rol: 'usuario', texto: textoUsuario }]);
        setInput('');
        setCargando(true);

        // Búsqueda segura del token CSRF
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = metaTag ? metaTag.getAttribute('content') : '';

        try {
            console.log("Enviando petición a /chat..."); // Log de depuración

            const respuesta = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ mensaje: textoUsuario })
            });

            // Depuración de respuesta del servidor
            if (!respuesta.ok) {
                const errorTexto = await respuesta.text();
                console.error("Error detectado en la respuesta del servidor:", {
                    status: respuesta.status,
                    body: errorTexto
                });
                throw new Error(`Error ${respuesta.status}: ${errorTexto}`);
            }

            const data = await respuesta.json();
            console.log("Respuesta recibida exitosamente:", data); // Log de depuración

            if (data.status === 'success') {
                setMensajes(prev => [...prev, { rol: 'bot', texto: data.respuesta }]);
            } else {
                setMensajes(prev => [...prev, { rol: 'bot', texto: 'El servidor respondió con error.' }]);
            }
        } catch (error) {
            console.error("Excepción en enviarMensaje:", error); // Log de depuración
            setMensajes(prev => [...prev, { rol: 'bot', texto: 'Error técnico: Revisa la consola (F12).' }]);
        } finally {
            setCargando(false);
        }
    };

    return (
        <div className="chat-container">
            <div className="chat-header">Agente CPPI</div>
            
            <div className="chat-messages">
                {mensajes.map((msg, index) => (
                    <div key={index} className={`mensaje ${msg.rol === 'usuario' ? 'mensaje-usuario' : 'mensaje-bot'}`}>
                    {/* 2. Envuelve el texto en ReactMarkdown */}
                    <ReactMarkdown>{msg.texto}</ReactMarkdown>
                    </div>
                ))}
                {cargando && (
                    <div className="mensaje mensaje-bot" style={{ opacity: 0.7 }}>Escribiendo...</div>
                )}
                <div ref={mensajesEndRef} />
            </div>

            <form onSubmit={enviarMensaje} className="chat-input-area">
                <input 
                    type="text" 
                    className="chat-input"
                    placeholder="Escribe tu consulta aquí..." 
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    disabled={cargando}
                />
                <button type="submit" className="btn-enviar" disabled={cargando || !input.trim()}>
                    Enviar
                </button>
            </form>
        </div>
    );
}