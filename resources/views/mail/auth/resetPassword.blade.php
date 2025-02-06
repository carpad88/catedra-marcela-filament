<x-mail::message title="Restablecimiento de contraseña">
Hola,

Estas recibiendo este email porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.

<x-mail::button :url="$url">
Reiniciar contraseña
</x-mail::button>

Este enlace de restablecimiento de contraseña caducará en 60 minutos.

Si tu no solicitaste un restablecimiento de contraseña, puedes ignorar este correo.
</x-mail::message>
