import { useState } from "react";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [mensaje, setMensaje] = useState("");
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e) => {
    e.preventDefault();
    setLoading(true);
    setMensaje("");

    try {
      const res = await fetch("http://localhost/vetlia/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          email,
          password
        })
      });

      const data = await res.json();

      if (data.success) {
        const user = data.user;

        // 🔐 guardar sesión
        localStorage.setItem("user", JSON.stringify(user));

        setMensaje("✅ Bienvenido " + user.nombre);

        // 🔥 REDIRECCIÓN SEGÚN ROL
        setTimeout(() => {
          if (!user.rol) {
            alert("Usuario sin rol definido");
            return;
          }

          if (user.rol === "admin") {
            window.location.href = "/dashboard.html";
          } 
          else if (user.rol === "veterinario") {
            window.location.href = "/veterinario.html";
          } 
          else if (user.rol === "paciente") {
            window.location.href = "paciente.html";
          } 
          else {
            alert("Rol no reconocido: " + user.rol);
          }
        }, 800);

      } else {
        setMensaje("❌ " + data.error);
      }

    } catch (error) {
      console.error(error);
      setMensaje("❌ Error de conexión con el servidor");
    }

    setLoading(false);
  };

  return (
    <div style={styles.container}>
      <form onSubmit={handleLogin} style={styles.card}>
        
        <h2>🐾 Vetlia</h2>

        <input
          type="email"
          placeholder="Correo"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          style={styles.input}
          required
        />

        <input
          type="password"
          placeholder="Contraseña"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          style={styles.input}
          required
        />

        <button type="submit" style={styles.button} disabled={loading}>
          {loading ? "Ingresando..." : "Iniciar sesión"}
        </button>

        {mensaje && <p style={styles.message}>{mensaje}</p>}

      </form>
    </div>
  );
}

// 🎨 ESTILOS
const styles = {
  container: {
    height: "100vh",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    background: "linear-gradient(135deg, #4facfe, #00f2fe)"
  },
  card: {
    background: "#ffffff",
    padding: "35px",
    borderRadius: "12px",
    boxShadow: "0 10px 25px rgba(0,0,0,0.2)",
    width: "320px",
    textAlign: "center"
  },
  input: {
    width: "100%",
    padding: "12px",
    margin: "10px 0",
    borderRadius: "6px",
    border: "1px solid #ccc"
  },
  button: {
    width: "100%",
    padding: "12px",
    background: "#28a745",
    color: "#fff",
    border: "none",
    borderRadius: "6px",
    cursor: "pointer",
    fontWeight: "bold"
  },
  message: {
    marginTop: "15px"
  }
};