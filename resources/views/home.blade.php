@extends('../themes/' . $activeTheme . '/' . $activeLayout)

@section('subhead')
<title>PRUEBA IMPRESIÓN</title>
@endsection

@section('subcontent')


<button id="btn">🖨️ Probar QZ</button>

<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.js"></script>

<script>
    
async function initQZ() {
    if (!qz.websocket.isActive()) {
        console.log('Conectando a QZ...');
        await qz.websocket.connect();
        console.log('Conectado a QZ');
    }
}

//Se conecta una sola vez al cargar
initQZ();

document.getElementById('btn').addEventListener('click', async () => {
    try {
        const printers = await qz.printers.find();
        console.log('Impresoras:', printers);

        const printer = printers.find(p => p.includes('XP'));
        console.log('Usando:', printer);

        // const config = qz.configs.create(printer);
        const config = qz.configs.create(printer, {
            size: { width: 58, height: 100 },
            units: 'mm',
            copies: 1,
            margins: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            },
            scaleContent: false
        });

        const data = [
            '\x1B\x40',
            '\x1B\x4D\x01',
            '___probando texto___\n',
            '1234567890123456789012345678901234567890123\n',
            '\n\n\n',
            '\x1D\x56\x00'
        ];

        await qz.print(config, data);

        alert('Impresión enviada');
    } catch (e) {
        console.error('ERROR:', e);
        alert('Error: ' + e);
    }
});
</script>


@endsection
