document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined') {
        const mapDiv = document.getElementById('map');
        const origemData = JSON.parse(mapDiv.getAttribute('data-origem'));
        const destinoData = JSON.parse(mapDiv.getAttribute('data-destino'));

        const map = L.map('map');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const origem = [origemData.latitude, origemData.longitude];
        const destino = [destinoData.latitude, destinoData.longitude];

        // Ícone personalizado para aeroportos
        const airportIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style=\"background-color: #ffcc00; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);\">✈️</div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        // Marcador de origem
        L.marker(origem, {icon: airportIcon})
            .bindPopup('<strong>' + origemData.cidade + ' (' + origemData.codigo + ')</strong><br>Aeroporto de Origem')
            .addTo(map);

        // Marcador de destino
        L.marker(destino, {icon: airportIcon})
            .bindPopup('<strong>' + destinoData.cidade + ' (' + destinoData.codigo + ')</strong><br>Aeroporto de Destino')
            .addTo(map);

        // Linha da rota
        const linha = L.polyline([origem, destino], {
            color: '#ffcc00',
            weight: 3,
            opacity: 0.8,
            dashArray: '10, 10',
            lineCap: 'round'
        }).addTo(map);

        // Ajustar visualização
        map.fitBounds(linha.getBounds(), {
            padding: [50, 50]
        });

        // Marcador da caixa animada
        const boxIcon = L.divIcon({
            className: 'box-marker',
            html: '<div style="font-size: 20px;">📦</div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
        
        const box = L.marker(origem, {icon: boxIcon}).addTo(map);
        
        // Animação da caixa
        let start = Date.now();
        const duration = 5000; // Aumentar a duração para uma animação mais lenta
        
        function animate() {
            const timeElapsed = Date.now() - start;
            const progress = Math.min(timeElapsed / duration, 1);
        
            const lat = origem[0] + (destino[0] - origem[0]) * progress;
            const lng = origem[1] + (destino[1] - origem[1]) * progress;
            box.setLatLng([lat, lng]);
        
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }
        
        animate();
    }
});
