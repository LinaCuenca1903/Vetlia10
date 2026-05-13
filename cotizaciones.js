// cotizaciones.js

let carrito = [];

async function cargarMascotas(){

    let r = await fetch(
      "http://localhost/vetlia/get_pacientes.php"
    );

    let data = await r.json();

    cot_mascota.innerHTML =
    `<option value="">Seleccionar mascota</option>`;

    data.forEach(m => {

        cot_mascota.innerHTML += `
            <option value="${m.id}">
                ${m.nombre}
            </option>
        `;
    });
}


async function cargarCatalogo(){

    let r = await fetch(
      "http://localhost/vetlia/get_catalogo.php"
    );

    let data = await r.json();

    catalogoContainer.innerHTML = "";

    data.forEach(item => {

        let icono = "📦";

        if(item.tipo == "medicina"){
            icono = "💊";
        }

        if(item.tipo == "servicio"){
            icono = "🩺";
        }

        catalogoContainer.innerHTML += `

            <div class="card">

                ${
                    item.imagen
                    ?
                    `<img src="uploads/${item.imagen}">`
                    :
                    `<div style="font-size:80px">${icono}</div>`
                }

                <h3>${item.nombre}</h3>

                <p>${item.tipo}</p>

                <p>$ ${item.precio}</p>

                <input
                    type="number"
                    id="cantidad_${item.id}"
                    min="1"
                    value="1"
                >

                <button onclick="agregarItem(
                    ${item.id},
                    '${item.nombre}',
                    ${item.precio}
                )">

                    Agregar

                </button>

            </div>
        `;
    });
}


function agregarItem(id,nombre,precio){

    let cantidad =
    document.getElementById(
        `cantidad_${id}`
    ).value;

    cantidad = parseInt(cantidad);

    let subtotal = cantidad * precio;

    carrito.push({
        catalogo_id:id,
        nombre:nombre,
        precio:precio,
        cantidad:cantidad,
        subtotal:subtotal
    });

    renderCarrito();
}


function renderCarrito(){

    detalleCotizacion.innerHTML = "";

    let total = 0;

    carrito.forEach((item,index) => {

        total += item.subtotal;

        detalleCotizacion.innerHTML += `

            <tr>

                <td>${item.nombre}</td>

                <td>${item.cantidad}</td>

                <td>$ ${item.precio}</td>

                <td>$ ${item.subtotal}</td>

                <td>

                    <button onclick="eliminarItem(${index})">
                        X
                    </button>

                </td>

            </tr>
        `;
    });

    totalCotizacion.innerText =
    "Total: $" + total;
}


function eliminarItem(index){

    carrito.splice(index,1);

    renderCarrito();
}


async function guardarCotizacion(){

    let user =
    JSON.parse(localStorage.getItem("user"));

    if(!cot_mascota.value){

        alert("Selecciona mascota");

        return;
    }

    if(carrito.length == 0){

        alert("Agrega items");

        return;
    }

    let total = 0;

    carrito.forEach(i => {
        total += i.subtotal;
    });

    let r = await fetch(
      "http://localhost/vetlia/crear_cotizacion.php",
      {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({

            mascota_id:cot_mascota.value,

            usuario_id:user.id,
            
            estado:cot_estado.value,

            total:total,

            items:carrito
        })
      }
    );

    let data = await r.json();

    console.log(data);

    if(data.success){

        alert("Cotización guardada");

        // LIMPIAR
        carrito = [];

        renderCarrito();

        cot_mascota.value = "";

        // LIMPIAR CANTIDADES
        document
        .querySelectorAll("input[type='number']")
        .forEach(i => {
            i.value = 1;
        });

        cargarCotizaciones();
    }
}

async function cargarCotizaciones(){

    if(!cot_mascota.value){

        listaCotizaciones.innerHTML = "";

        return;
    }

    let r = await fetch(

      "http://localhost/vetlia/get_cotizaciones.php?mascota_id="
      + cot_mascota.value

    );

    let data = await r.json();

    listaCotizaciones.innerHTML = "";

    data.forEach(c => {

        listaCotizaciones.innerHTML += `

            <div class="card">

                <h3>
                    Cotización #${c.id}
                </h3>

                <p>
                    Fecha: ${c.fecha}
                </p>

                <p>
                    Total: $${c.total}
                </p>

                <p>
                    Estado: ${c.estado}
                </p>

                <button onclick="descargarPDF(${c.id})">

                    PDF

                </button>

                <input
           type="email"
           id="correo_${c.id}"
           placeholder="Correo"
        >

        <button onclick="enviarCorreo(${c.id})">

            Enviar PDF

        </button>


            </div>
        `;
    });
}

function descargarPDF(id){

    window.open(

      "http://localhost/vetlia/generar_pdf.php?id="
      + id,

      "_blank"
    );
}

async function enviarCorreo(id){

    let correo =
    document.getElementById(
        "correo_" + id
    ).value;

    if(!correo){

        alert("Ingresa correo");

        return;
    }

    let r = await fetch(
      "http://localhost/vetlia/enviar_cotizacion.php",
      {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({
            id:id,
            correo:correo
        })
      }
    );

    let data = await r.json();

    if(data.success){

        alert("Correo enviado");
    }
}

cargarMascotas();
cargarCatalogo();