# Pago Seguro - Webcheckout - Prestashop 1.7

Módulo para pagos en línea por medio de Pago Seguro en modo Webcheckout(se redirige a la pasarela de pagos de Pago Seguro y una vez se procesa el pedido se retorna una respuesta al Prestashop), este módulo fue desarrollado para Colombia, pero debería funcionar
en: (Argentina, Brasil, Chile, Colombia, México, Panamá, Perú)

[Manual de Instalación](https://pagosegurorepos.github.io/documentation/#/plugins/prestashop)

## Notas

Se toma la base de ejemplo `https://github.com/PrestaShop/paymentexample` dada en la documentación de [Prestashop](http://doc.prestashop.com/display/PS17/Creating+a+PrestaShop+1.7+Payment+Module) y se adapta las funcionalidades [Webcheckout](http://3.15.12.108:8000/pagoseguro/) de Pago Seguro.

## Traducciones

Las traducciones están en Español Colombia e Ingles, las puedes cambiar al idioma que necesites ejemplo:

- `translations/es.php` -> `translations/es.php`
- `translations/en.php` -> `translations/en.php`

## Instalación

- Descargar el contenido de este [repositorio](https://github.com/pagosegurorepos/plugin-prestashop/archive/master.zip) en un folder llamado `pagoseguro` dentro de la carpeta de modulos.
- En el administrador de Prestashop ir a la sección de módulos y buscar por "Pago Seguro" e instalar.
- Entrar en las configuraciones del módulo e ingresar los datos solicitados.
- Revisar si esta activo en el administrador/pago/preferencias/Restricciones por transportista y activar en todos los transportistas

## Pruebas de compras

- En la configuración del módulo habilitar el modo Test en Si
- Buscar generadores de códigos de creditcards.
