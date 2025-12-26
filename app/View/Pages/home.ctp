<?php
/**
 * Dashboard CakePHP com Animação WebGL
 */
$this->layout = false; // Desativa o layout padrão do Cake para usarmos tela cheia
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vendas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #000; }
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
        .glass-panel { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="font-sans text-white">

    <div id="canvas-container"></div>
    
    <div class="absolute inset-0 bg-black/60 z-0 pointer-events-none"></div>

    <div class="relative z-10 flex h-screen overflow-hidden">
        
        <aside class="w-64 border-r border-white/10 flex flex-col p-6 glass-panel hidden md:flex">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-8 h-8 bg-blue-500 rounded-full shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                <div>
                    <h1 class="font-bold text-sm">Gestão Vendas</h1>
                    <p class="text-xs text-gray-400">Administrador</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a href="#" class="block px-4 py-2 bg-blue-600/20 text-blue-400 border border-blue-500/30 rounded-lg">Dashboard</a>
                <a href="#" class="block px-4 py-2 hover:bg-white/5 text-gray-400 rounded-lg transition">Relatórios</a>
                <a href="#" class="block px-4 py-2 hover:bg-white/5 text-gray-400 rounded-lg transition">Corbans</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold drop-shadow-md">Visão Geral</h2>
                    <p class="text-gray-300">Monitoramento em tempo real (Renderizado pelo PHP).</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg text-sm transition shadow-lg shadow-blue-500/20">
                    Atualizar Dados
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="glass-panel p-5 rounded-xl border-l-4 border-green-500">
                    <p class="text-gray-400 text-xs uppercase">Volume Total</p>
                    <h3 class="text-2xl font-bold mt-1">R$ 12.4M</h3>
                </div>
                <div class="glass-panel p-5 rounded-xl border-l-4 border-blue-500">
                    <p class="text-gray-400 text-xs uppercase">Comissão</p>
                    <h3 class="text-2xl font-bold mt-1">R$ 840k</h3>
                </div>
                <div class="glass-panel p-5 rounded-xl border-l-4 border-purple-500">
                    <p class="text-gray-400 text-xs uppercase">Corbans Ativos</p>
                    <h3 class="text-2xl font-bold mt-1">142</h3>
                </div>
                <div class="glass-panel p-5 rounded-xl border-l-4 border-orange-500">
                    <p class="text-gray-400 text-xs uppercase">Ticket Médio</p>
                    <h3 class="text-2xl font-bold mt-1">R$ 4.2k</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="glass-panel p-6 rounded-xl shadow-xl flex flex-col items-center justify-center">
                    <h3 class="font-bold mb-4 w-full text-left">Meta Mensal</h3>
                    <div class="relative w-40 h-40 rounded-full border-8 border-white/10 flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full border-8 border-blue-500 border-t-transparent animate-spin-slow" style="animation-duration: 3s;"></div>
                        <span class="text-4xl font-bold text-blue-400">78%</span>
                    </div>
                </div>

                <div class="glass-panel p-6 rounded-xl col-span-2 shadow-xl">
                    <h3 class="font-bold mb-4">Ranking Top 3 (PHP Loop)</h3>
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-400 border-b border-white/10">
                                <th class="pb-2">Nome</th>
                                <th class="pb-2">Volume</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $ranking = [
                                ['nome' => 'João Silva', 'vol' => 'R$ 2.1M', 'status' => 'Crescimento'],
                                ['nome' => 'Maria Souza', 'vol' => 'R$ 1.8M', 'status' => 'Queda'],
                                ['nome' => 'Pedro Santos', 'vol' => 'R$ 1.5M', 'status' => 'Estável']
                            ];
                            foreach($ranking as $r): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="py-3"><?php echo $r['nome']; ?></td>
                                <td class="py-3 font-bold"><?php echo $r['vol']; ?></td>
                                <td class="py-3 text-green-400"><?php echo $r['status']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script type="module">
        import { Renderer, Camera, Transform, Plane, Program, Mesh, Vec2 } from 'https://unpkg.com/ogl@1.0.0/src/index.mjs';

        const container = document.getElementById('canvas-container');
        
        const renderer = new Renderer({ alpha: true });
        const gl = renderer.gl;
        container.appendChild(gl.canvas);

        // Ajusta tamanho
        function resize() {
            renderer.setSize(window.innerWidth, window.innerHeight);
        }
        window.addEventListener('resize', resize, false);
        resize();

        // Shaders (A mágica das cores e movimento)
        const vertex = `
            attribute vec3 position;
            attribute vec2 uv;
            varying vec2 vUv;
            void main() {
                vUv = uv;
                gl_Position = vec4(position, 1.0);
            }
        `;

        const fragment = `
            precision highp float;
            uniform float uTime;
            uniform vec2 uResolution;
            varying vec2 vUv;

            // Cores Neon (Roxo, Azul, Rosa)
            const vec3 c1 = vec3(0.29, 0.11, 0.58); // #4c1d95
            const vec3 c2 = vec3(0.19, 0.18, 0.50); // #312e81
            const vec3 c3 = vec3(0.74, 0.09, 0.36); // #be185d

            void main() {
                vec2 uv = vUv;
                
                // Criação do gradiente diagonal
                float noise = sin(uv.x * 10.0 + uTime * 0.5) * 0.1;
                float gradient = uv.x + uv.y + noise;
                
                // Efeito de "Blinds" (Persianas)
                float blind = sin((uv.x + uv.y) * 40.0 - uTime);
                blind = smoothstep(-0.2, 0.2, blind); // Suaviza
                
                // Mistura das cores
                vec3 finalColor = mix(c1, c2, uv.y);
                finalColor = mix(finalColor, c3, uv.x * 0.8 + sin(uTime * 0.2)*0.2);
                
                // Aplica o efeito de luz das persianas
                finalColor += blind * 0.05; 

                gl_FragColor = vec4(finalColor, 1.0);
            }
        `;

        const program = new Program(gl, {
            vertex,
            fragment,
            uniforms: {
                uTime: { value: 0 },
                uResolution: { value: new Vec2(window.innerWidth, window.innerHeight) },
            },
        });

        const mesh = new Mesh(gl, { geometry: new Plane(gl), program });

        function update(t) {
            requestAnimationFrame(update);
            program.uniforms.uTime.value = t * 0.001;
            renderer.render({ scene: mesh });
        }
        
        requestAnimationFrame(update);
    </script>
</body>
</html>