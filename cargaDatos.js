// Función de inicio que se ejecuta cuando la página se carga
function inicio() {
    // Agrego un listener al campo de legajo para cargar los datos del estudiante
    document.getElementById('legajo').addEventListener('input', cargarEstudiante);

    // Agrego un listener al formulario para registrar la inscripción al enviar el formulario
    document.getElementById('formInscripcion').addEventListener('submit', async (e) => {
        e.preventDefault();  // Evito que el formulario se envíe de manera convencional
        registrarInscripcion();  // Llamo a la función que registra la inscripción
    });

    // Cargo las charlas disponibles al cargar la página
    cargarCharlas();
}

// Función para cargar las charlas disponibles desde el backend
async function cargarCharlas() {
    try {
        const response = await fetch('obtenerCharlas.php');
        const data = await response.json();
        const selectCharla = document.getElementById('charla');

        // Limpio el contenido previo del select y agrego la opción por defecto
        selectCharla.innerHTML = '<option value="">Seleccione una charla</option>';

        // Itero sobre las charlas recibidas y las agrego al select
        data.forEach(charla => {
            const option = document.createElement('option');
            option.value = charla.id;
            option.textContent = charla.titulo;
            selectCharla.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar charlas:', error);
    }
}

// Función que se ejecuta al ingresar un legajo para cargar los datos del estudiante
async function cargarEstudiante() {
    const legajo = document.getElementById('legajo').value;
    if (legajo) {
        try {
            const response = await fetch(`obtenerEstudiante.php?legajo=${legajo}`);
            const data = await response.json();

            // Si el estudiante existe, completo los campos con sus datos y los deshabilito
            if (data.status === 'success') {
                document.getElementById('nombre').value = data.nombre_completo;
                document.getElementById('mail').value = data.mail;
                document.getElementById('nombre').disabled = true;
                document.getElementById('mail').disabled = true;
            } else {
                // Si el estudiante no existe, habilito los campos para que se puedan llenar
                document.getElementById('nombre').disabled = false;
                document.getElementById('mail').disabled = false;
            }
        } catch (error) {
            console.error('Error al cargar estudiante:', error);
        }
    }
}

// Función para registrar la inscripción del estudiante

async function registrarInscripcion() {
    const formData = new FormData(document.getElementById('formInscripcion'));

    // Asegúrate de que el legajo esté incluido en el FormData
    const legajo = document.getElementById('legajo').value;
    if (legajo) {
        formData.append('legajo', legajo);  // Agrego el legajo manualmente al FormData
    }

    try {
        const response = await fetch('inscribirEstudiante.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        // Verifico si hay errores o si la inscripción fue exitosa
        if (data.status === 'error') {
            mostrarErrores(data.message);  // Si hay errores, los muestro
        } else {
            alert('Inscripción realizada con éxito');
            document.getElementById('formInscripcion').reset();
        }
    } catch (error) {
        console.error('Error al registrar inscripción:', error);
    }
}

// Función para mostrar los errores en el formulario
function mostrarErrores(errores) {
    // Muestro el error general, si lo hay
    const errorGeneralElemento = document.getElementById("errorGeneral");
    if (errores.errorGeneral && errorGeneralElemento) {
        errorGeneralElemento.innerText = errores.errorGeneral;
        errorGeneralElemento.classList.remove("d-none");
    }

    // Muestro los errores específicos de cada campo
    Object.keys(errores).forEach(campo => {
        if (campo !== "errorGeneral") {
            const elementoError = document.getElementById(campo);
            if (elementoError) {
                elementoError.innerText = errores[campo];
                elementoError.classList.remove("d-none");
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', inicio);

