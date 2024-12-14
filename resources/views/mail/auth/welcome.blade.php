<x-mail::message>
Hola **{{  $user->first_name }}**,

Has sido invitado a formar parte de la comunidad de Cátedra Marcela

Da click en el siguiente link para que escribas una contraseña nueva y actives tu cuenta.

<x-mail::button :url="$url">
Activar cuenta
</x-mail::button>

El objetivo de la cátedra es prepararte para que identifiques los diferentes productos editoriales de acuerdo a su contenido, mercado, estructura y organización de información.
</x-mail::message>
