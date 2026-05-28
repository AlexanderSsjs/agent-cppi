import React, { useState, useRef, useEffect } from "react";
import ReactMarkdown from "react-markdown";
import rehypeSanitize from "rehype-sanitize";

export default function ChatInterface() {
    const [mensajes, setMensajes] = useState([
        {
            rol: "bot",
            texto: "¡Hola! Soy el asistente virtual de Ingeniería Líder 😊 ¿En qué curso o proceso de inscripción puedo ayudarte?",
            hora: obtenerHora(),
        },
    ]);

    const [input, setInput] = useState("");
    const [cargando, setCargando] = useState(false);

    const mensajesEndRef = useRef(null);

    // Scroll automático
    useEffect(() => {
        mensajesEndRef.current?.scrollIntoView({
            behavior: "smooth",
        });
    }, [mensajes, cargando]);

    function obtenerHora() {
        return new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    const enviarMensaje = async (e) => {
        e.preventDefault();

        const textoUsuario = input.trim();

        // Validaciones
        if (!textoUsuario || textoUsuario.length < 2 || cargando) return;

        // Agregar mensaje usuario
        const nuevoMensajeUsuario = {
            rol: "usuario",
            texto: textoUsuario,
            hora: obtenerHora(),
        };

        setMensajes((prev) => [...prev, nuevoMensajeUsuario]);

        setInput("");
        setCargando(true);

        // Obtener token CSRF
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";

        try {
            const respuesta = await fetch("/chat", {
                method: "POST",

                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },

                body: JSON.stringify({
                    mensaje: textoUsuario,

                    // Solo últimos 10 mensajes
                    historial: mensajes.slice(-10),
                }),
            });

            if (!respuesta.ok) {
                const errorTexto = await respuesta.text();

                console.error("Error servidor:", {
                    status: respuesta.status,
                    body: errorTexto,
                });

                throw new Error("Error del servidor");
            }

            const data = await respuesta.json();

            if (data.status === "success") {
                setMensajes((prev) => [
                    ...prev,

                    {
                        rol: "bot",
                        texto: data.respuesta,
                        hora: obtenerHora(),
                    },
                ]);
            } else {
                setMensajes((prev) => [
                    ...prev,

                    {
                        rol: "bot",
                        texto: "Ocurrió un problema al procesar tu consulta.",
                        hora: obtenerHora(),
                    },
                ]);
            }
        } catch (error) {
            console.error("Error:", error);

            setMensajes((prev) => [
                ...prev,

                {
                    rol: "bot",
                    texto: "⚠️ Ocurrió un problema de conexión. Intenta nuevamente en unos segundos.",
                    hora: obtenerHora(),
                },
            ]);
        } finally {
            setCargando(false);
        }
    };

    return (
        <div className="chat-container">
            <div className="chat-header">Ingeniería Líder</div>

            <div className="chat-messages">
                {mensajes.map((msg, index) => (
                    <div
                        key={index}
                        className={`mensaje ${
                            msg.rol === "usuario"
                                ? "mensaje-usuario"
                                : "mensaje-bot"
                        }`}
                    >
                        <div className="mensaje-contenido">
                            <ReactMarkdown rehypePlugins={[rehypeSanitize]}>
                                {msg.texto}
                            </ReactMarkdown>
                        </div>

                        <div className="mensaje-hora">{msg.hora}</div>
                    </div>
                ))}

                {cargando && (
                    <div className="mensaje mensaje-bot">
                        <div className="typing">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                )}

                <div ref={mensajesEndRef} />
            </div>

            <form onSubmit={enviarMensaje} className="chat-input-area">
                <textarea
                    className="chat-input"
                    placeholder="Escribe tu consulta aquí..."
                    value={input}
                    disabled={cargando}
                    onChange={(e) => setInput(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === "Enter" && !e.shiftKey) {
                            e.preventDefault();
                            enviarMensaje(e);
                        }
                    }}
                    rows={1}
                />

                <button
                    type="submit"
                    className="btn-enviar"
                    disabled={cargando || !input.trim()}
                >
                    {cargando ? "..." : "Enviar"}
                </button>
            </form>
        </div>
    );
}
