<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Rastreamento</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #444;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
        }

        .form-group input:focus {
            border-color: #ffcc00;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 204, 0, 0.1);
        }

        .btn {
            background-color: #ffcc00;
            color: black;
            border: none;
            padding: 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #e6b800;
        }

        .ocorrencia {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .tipo-badge {
            display: block;
            color: #000;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .data {
            display: block;
            color: #000;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .descricao {
            display: block;
            color: #000;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .obs {
            display: block;
            color: #333;
            font-size: 14px;
            margin: 8px 0;
        }

        .recebedor {
            display: block;
            color: #333;
            font-style: italic;
            font-size: 14px;
            margin-top: 8px;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
        }

        #map {
            height: 400px;
            width: 100%;
            margin: 20px 0;
            border-radius: 8px;
            border: 1px solid #eee;
            z-index: 1;
        }

        .map-container {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .map-info {
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 5px;
            font-size: 14px;
            line-height: 1.5;
        }

        .map-info strong {
            color: #333;
        }

        .timeline {
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
            border-left: 2px solid #ffcc00;
        }

        .timeline-dot {
            position: absolute;
            left: -6px;
            top: 0;
            width: 10px;
            height: 10px;
            background: #ffcc00;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Consulta de Rastreamento</h1>
        <form method="POST">
            <div class="form-group">
                <label for="cnpj">CNPJ ou CPF</label>
                <input type="text" id="cnpj" name="cnpj" placeholder="Digite o CNPJ/CPF" required>
            </div>
            <div class="form-group">
                <label for="numero">Número da Nota Fiscal</label>
                <input type="text" id="numero" name="numero" placeholder="Digite o número da NF" required>
            </div>
            <button type="submit" name="consultar" class="btn">Buscar Rastreamento</button>
        </form>

        <?php
        // Definição dos aeroportos com coordenadas reais
        $aeroportos = [
            // Centro-Oeste
            'GYN' => ['cidade' => 'Goiânia', 'uf' => 'GO', 'latitude' => -16.6312, 'longitude' => -49.2201],
            'RVD' => ['cidade' => 'Rio Verde', 'uf' => 'GO', 'latitude' => -17.8347, 'longitude' => -50.9192],
            'CLV' => ['cidade' => 'Caldas Novas', 'uf' => 'GO', 'latitude' => -17.7247, 'longitude' => -48.6247],
            'TLZ' => ['cidade' => 'Catalão', 'uf' => 'GO', 'latitude' => -18.1658, 'longitude' => -47.9444],
            'BSB' => ['cidade' => 'Brasília', 'uf' => 'DF', 'latitude' => -15.8692, 'longitude' => -47.9208],
            'AFL' => ['cidade' => 'Alta Floresta', 'uf' => 'MT', 'latitude' => -9.8661, 'longitude' => -56.1058],
            'BPG' => ['cidade' => 'Barra do Garças', 'uf' => 'MT', 'latitude' => -15.8614, 'longitude' => -52.3886],
            'CCX' => ['cidade' => 'Cáceres', 'uf' => 'MT', 'latitude' => -16.0431, 'longitude' => -57.6792],
            'CGB' => ['cidade' => 'Cuiabá', 'uf' => 'MT', 'latitude' => -15.6529, 'longitude' => -56.1167],
            'ROO' => ['cidade' => 'Rondonópolis', 'uf' => 'MT', 'latitude' => -16.4667, 'longitude' => -54.6333],
            'TGQ' => ['cidade' => 'Tangará da Serra', 'uf' => 'MT', 'latitude' => -14.6196, 'longitude' => -57.4333],
            'OPS' => ['cidade' => 'Sinop', 'uf' => 'MT', 'latitude' => -11.8581, 'longitude' => -55.5094],
            'SMT' => ['cidade' => 'Sorriso', 'uf' => 'MT', 'latitude' => -12.5425, 'longitude' => -55.7172],
            'BYO' => ['cidade' => 'Bonito', 'uf' => 'MS', 'latitude' => -21.1261, 'longitude' => -56.4514],
            'CSS' => ['cidade' => 'Cassilândia', 'uf' => 'MS', 'latitude' => -19.1128, 'longitude' => -51.7308],
            'CGR' => ['cidade' => 'Campo Grande', 'uf' => 'MS', 'latitude' => -20.4686, 'longitude' => -54.6725],
            'CMG' => ['cidade' => 'Corumbá', 'uf' => 'MS', 'latitude' => -19.0119, 'longitude' => -57.6714],
            'DOU' => ['cidade' => 'Dourados', 'uf' => 'MS', 'latitude' => -22.2019, 'longitude' => -54.8019],
            'PBB' => ['cidade' => 'Paranaíba', 'uf' => 'MS', 'latitude' => -19.7506, 'longitude' => -51.1911],
            'PMG' => ['cidade' => 'Ponta Porã', 'uf' => 'MS', 'latitude' => -22.5497, 'longitude' => -55.7025],
            'TJL' => ['cidade' => 'Três Lagoas', 'uf' => 'MS', 'latitude' => -20.7542, 'longitude' => -51.6789],

            // Norte
            'CZS' => ['cidade' => 'Cruzeiro do Sul', 'uf' => 'AC', 'latitude' => -7.5994, 'longitude' => -72.7692],
            'RBR' => ['cidade' => 'Rio Branco', 'uf' => 'AC', 'latitude' => -9.9689, 'longitude' => -67.8981],
            'MCP' => ['cidade' => 'Macapá', 'uf' => 'AP', 'latitude' => 0.0506, 'longitude' => -51.0722],
            'MAO' => ['cidade' => 'Manaus', 'uf' => 'AM', 'latitude' => -3.0389, 'longitude' => -60.0497],
            'ATM' => ['cidade' => 'Altamira', 'uf' => 'PA', 'latitude' => -3.2089, 'longitude' => -52.2242],
            'BEL' => ['cidade' => 'Belém', 'uf' => 'PA', 'latitude' => -1.3792, 'longitude' => -48.4783],
            'CKS' => ['cidade' => 'Carajás', 'uf' => 'PA', 'latitude' => -6.1153, 'longitude' => -50.0014],
            'STM' => ['cidade' => 'Santarém', 'uf' => 'PA', 'latitude' => -2.4228, 'longitude' => -54.7931],
            'PVH' => ['cidade' => 'Porto Velho', 'uf' => 'RO', 'latitude' => -8.7619, 'longitude' => -63.9039],
            'BVB' => ['cidade' => 'Boa Vista', 'uf' => 'RR', 'latitude' => 2.8414, 'longitude' => -60.6922],
            'PMW' => ['cidade' => 'Palmas', 'uf' => 'TO', 'latitude' => -10.2419, 'longitude' => -48.3564],

            // Nordeste
            'MCZ' => ['cidade' => 'Maceió', 'uf' => 'AL', 'latitude' => -9.5108, 'longitude' => -35.7931],
            'SSA' => ['cidade' => 'Salvador', 'uf' => 'BA', 'latitude' => -12.9086, 'longitude' => -38.3225],
            'FOR' => ['cidade' => 'Fortaleza', 'uf' => 'CE', 'latitude' => -3.7762, 'longitude' => -38.5323],
            'SLZ' => ['cidade' => 'São Luís', 'uf' => 'MA', 'latitude' => -2.5894, 'longitude' => -44.2342],
            'JPA' => ['cidade' => 'João Pessoa', 'uf' => 'PB', 'latitude' => -7.1464, 'longitude' => -34.9486],
            'REC' => ['cidade' => 'Recife', 'uf' => 'PE', 'latitude' => -8.1264, 'longitude' => -34.9235],
            'THE' => ['cidade' => 'Teresina', 'uf' => 'PI', 'latitude' => -5.0594, 'longitude' => -42.8233],
            'NAT' => ['cidade' => 'Natal', 'uf' => 'RN', 'latitude' => -5.7681, 'longitude' => -35.1967],
            'AJU' => ['cidade' => 'Aracaju', 'uf' => 'SE', 'latitude' => -10.9078, 'longitude' => -37.0706],

            // Sudeste
            'VIX' => ['cidade' => 'Vitória', 'uf' => 'ES', 'latitude' => -20.2576, 'longitude' => -40.2835],
            'CNF' => ['cidade' => 'Confins', 'uf' => 'MG', 'latitude' => -19.6244, 'longitude' => -43.9719],
            'PLU' => ['cidade' => 'Belo Horizonte', 'uf' => 'MG', 'latitude' => -19.8517, 'longitude' => -43.9505],
            'GIG' => ['cidade' => 'Rio de Janeiro', 'uf' => 'RJ', 'latitude' => -22.8089, 'longitude' => -43.2436],
            'SDU' => ['cidade' => 'Rio de Janeiro', 'uf' => 'RJ', 'latitude' => -22.9111, 'longitude' => -43.1633],
            'GRU' => ['cidade' => 'Guarulhos', 'uf' => 'SP', 'latitude' => -23.4356, 'longitude' => -46.4731],
            'CGH' => ['cidade' => 'São Paulo', 'uf' => 'SP', 'latitude' => -23.6261, 'longitude' => -46.6553],
            'VCP' => ['cidade' => 'Campinas', 'uf' => 'SP', 'latitude' => -23.0074, 'longitude' => -47.1345],

            // Sul
            'CWB' => ['cidade' => 'Curitiba', 'uf' => 'PR', 'latitude' => -25.5327, 'longitude' => -49.1758],
            'IGU' => ['cidade' => 'Foz do Iguaçu', 'uf' => 'PR', 'latitude' => -25.5960, 'longitude' => -54.4869],
            'POA' => ['cidade' => 'Porto Alegre', 'uf' => 'RS', 'latitude' => -29.9939, 'longitude' => -51.1711],
            'FLN' => ['cidade' => 'Florianópolis', 'uf' => 'SC', 'latitude' => -27.6704, 'longitude' => -48.5475]
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consultar'])) {
            $usuario = "3dffbe67a3bb4fd79a9b48dc1e4e532a";
            $senha = "344d09ae60947f5148f10802a461357635f5a47207c69bb6702f4e80106c6203";
            $base_url = "https://transcourierbh.brudam.com.br/api/v1";

            try {
                // Autenticação
                $auth_url = $base_url . "/acesso/auth/login";
                $auth_data = json_encode([
                    "usuario" => $usuario,
                    "senha" => $senha
                ]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $auth_url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $auth_data,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ],
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);

                $response = curl_exec($ch);
                $auth_result = json_decode($response, true);
                curl_close($ch);

                if (!isset($auth_result['data']['access_key'])) {
                    throw new Exception("Erro na autenticação");
                }

                $token = $auth_result['data']['access_key'];

                // Consulta de rastreamento
                $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);
                $numero = $_POST['numero'];

                $tracking_url = $base_url . "/tracking/ocorrencias/cnpj/nf?documento=" . urlencode($cnpj) .
                    "&numero=" . urlencode($numero) . "&tipo=cliente";

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $tracking_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token,
                        'Accept: application/json'
                    ],
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);

                $response = curl_exec($ch);
                $resultado = json_decode($response, true);
                curl_close($ch);

                if ($resultado['status'] === 1 && !empty($resultado['data'])) {
                    $origem = null;
                    $destino = null;

                    // Analisar ocorrências para identificar origem e destino
                    foreach ($resultado['data'] as $doc) {
                        if (isset($doc['dados']) && is_array($doc['dados'])) {
                            foreach ($doc['dados'] as $ocorrencia) {
                                // Buscar códigos de aeroporto na descrição e observações
                                foreach ($aeroportos as $codigo => $info) {
                                    if (
                                        strpos($ocorrencia['descricao'], "($codigo)") !== false ||
                                        (!empty($ocorrencia['obs']) && strpos($ocorrencia['obs'], $codigo) !== false)
                                    ) {
                                        if (!$origem) {
                                            $origem = $codigo;
                                        } else if (!$destino && $codigo !== $origem) {
                                            $destino = $codigo;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Se identificou origem e destino, mostrar o mapa
                    if ($origem && $destino && isset($aeroportos[$origem]) && isset($aeroportos[$destino])) {
                        echo '<div class="map-container">';
                        echo '<div class="map-info">';
                        echo '<div><strong>Origem:</strong> ' . $aeroportos[$origem]['cidade'] . ' - ' . $aeroportos[$origem]['uf'] . ' (' . $origem . ')</div>';
                        echo '<div><strong>Destino:</strong> ' . $aeroportos[$destino]['cidade'] . ' - ' . $aeroportos[$destino]['uf'] . ' (' . $destino . ')</div>';
                        echo '</div>';
                        echo '<div id="map" data-origem=\'' . json_encode(['latitude' => $aeroportos[$origem]['latitude'], 'longitude' => $aeroportos[$origem]['longitude']]) . '\' data-destino=\'' . json_encode(['latitude' => $aeroportos[$destino]['latitude'], 'longitude' => $aeroportos[$destino]['longitude']]) . '\'></div>';
                        echo '</div>';
                    }

                    // Timeline das ocorrências
                    echo '<div class="timeline">';
                    foreach ($resultado['data'] as $doc) {
                        if (isset($doc['dados']) && is_array($doc['dados'])) {
                            foreach ($doc['dados'] as $ocorrencia) {
                                echo "<div class='timeline-item'>";
                                echo "<div class='timeline-dot'></div>";
                                echo "<div class='tipo-badge'>" . htmlspecialchars($ocorrencia['tipo']) . "</div>";
                                echo "<div class='data'>" . date('d/m/Y H:i', strtotime($ocorrencia['data'])) . "</div>";
                                echo "<div class='descricao'>" . htmlspecialchars($ocorrencia['descricao']) . "</div>";

                                if (!empty($ocorrencia['obs'])) {
                                    echo "<div class='obs'>Obs: " . htmlspecialchars($ocorrencia['obs']) . "</div>";
                                }

                                if (!empty($ocorrencia['entrega_nome'])) {
                                    echo "<div class='recebedor'>Recebedor: " . htmlspecialchars($ocorrencia['entrega_nome']);
                                    if (!empty($ocorrencia['entrega_grau'])) {
                                        echo " (" . htmlspecialchars($ocorrencia['entrega_grau']) . ")";
                                    }
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }
                    }
                    echo '</div>';
                } else {
                    echo "<div class='error'>Nenhuma ocorrência encontrada para os dados informados.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='error'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>

        <?php if (isset($origem) && isset($destino)): ?>
            <script src="map.js"></script>
        <?php endif; ?>
        <script>
            document.getElementById('cnpj').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                }
                e.target.value = value;
            });
        </script>
    </div>
</body>

</html>
