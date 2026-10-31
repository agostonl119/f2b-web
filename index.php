<?php
session_start();

$version = '1.0';

// Nyelv kezelése
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'hu';
$allowed_langs = ['hu', 'en', 'zh'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'hu';
}
$_SESSION['lang'] = $lang;

// Külső nyelvi fájl betöltése
$file_path = __DIR__ . "/lang/{$lang}.php";
$t = file_exists($file_path) ? include $file_path : include __DIR__ . "/lang/hu.php";
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { darkbg: '#0f172a', darkcard: '#1e293b' } }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-darkbg text-slate-100 min-h-screen font-sans antialiased flex flex-col justify-between">

    <div class="max-w-7xl mx-auto px-4 py-6 w-full">
        <!-- Felső fejléc az eredeti SVG logóval -->
        <header class="flex flex-col md:flex-row justify-between items-center mb-6 bg-darkcard p-6 rounded-2xl shadow-lg border border-slate-800 gap-4">
            <div class="flex items-center space-x-4">
                <div class="p-3 rounded-xl text-white">
                    <svg width="50px" height="50px" viewBox="0 0 297 210" version="1.1" id="svg1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                        <defs id="defs1" />
                        <g id="layer1">
                            <path style="fill:#1693df;fill-opacity:1;stroke:#1692df;stroke-width:0.585817;stroke-opacity:1"
                                 d="m 101.33397,202.35856 c -2.094296,-0.48766 -3.93961,-1.04997 -4.100713,-1.24959 -0.161095,-0.1996 -1.545092,-0.99269 -3.075539,-1.76242 -1.530437,-0.76973 -2.782619,-1.60714 -2.782619,-1.86094 0,-0.25381 -0.38915,-0.5467 -0.864769,-0.65087 -0.839237,-0.18382 -6.854714,-5.50628 -13.910689,-12.30814 -1.900769,-1.8323 -3.671217,-3.41401 -3.934324,-3.51489 -0.697197,-0.26732 -11.119248,-9.90392 -17.146231,-15.854 -2.855674,-2.81923 -5.388278,-5.12589 -5.628012,-5.12589 -0.239732,0 -1.803653,-1.38399 -3.475378,-3.07554 -3.554389,-3.59653 -8.088194,-7.67946 -9.960864,-8.97031 -0.724948,-0.4997 -1.318089,-1.28903 -1.318089,-1.75404 0,-0.46505 -0.25483,-0.84551 -0.566294,-0.84551 -0.311456,0 -1.466518,-0.85677 -2.566804,-1.9039 -1.100277,-1.04716 -3.775238,-3.48561 -5.94434,-5.4188 -5.570713,-4.96487 -16.9913824,-16.29365 -16.9913824,-16.85471 0,-0.25828 -0.3086263,-0.66035 -0.6858228,-0.89347 -0.3772058,-0.23313 -0.8671176,-1.14618 -1.0886836,-2.029 -0.2215752,-0.88282 -0.6031634,-1.60512 -0.8479806,-1.60512 -0.8569665,0 -2.3209236,-6.77734 -2.4160825,-11.18514 C 3.9272881,100.76849 4.9755524,96.599888 7.3166212,92.423807 8.0225242,91.16458 15.427595,83.254575 23.772319,74.846013 32.117044,66.437459 50.390809,48.020418 64.380681,33.91926 92.00119,6.0790371 92.042432,6.0449359 100.74563,3.8508872 c 6.36196,-1.6038286 13.83789,-0.3646723 20.37925,3.3779189 2.84503,1.6277612 21.08708,19.3262639 39.21401,38.0455799 1.6827,1.737697 3.39621,3.449407 3.8078,3.803803 0.41159,0.354395 1.59211,1.540674 2.62338,2.636174 1.03128,1.095499 4.51083,4.60609 7.73232,7.801313 l 5.85728,5.809493 -1.16296,1.633239 c -2.52524,3.546372 -11.69285,12.056884 -15.226,14.134683 -4.84348,2.84836 -9.15236,3.045205 -13.98158,0.63871 -3.63124,-1.809527 -12.46444,-10.092082 -18.48563,-17.333269 -7.345,-8.833231 -13.60557,-12.113817 -23.13974,-12.125413 -5.88161,-0.0071 -10.875175,1.287101 -15.171378,3.932188 -2.349444,1.446501 -30.311291,29.010699 -33.65806,33.179396 -4.622633,5.757903 -6.486232,15.075397 -4.522279,22.610187 0.869035,3.33409 4.039239,9.95887 4.765687,9.95887 0.30201,0 0.549108,0.29897 0.549108,0.66437 0,0.36541 1.504815,2.01302 3.344033,3.66136 3.677508,3.29584 10.904224,10.05814 17.729457,16.59005 8.871462,8.49024 11.220639,10.37092 15.335861,12.27754 4.073931,1.88748 8.284391,2.48998 13.677931,1.95729 8.09532,-0.79955 13.03966,-3.34518 19.19721,-9.88385 13.95655,-14.82037 17.59558,-17.66012 24.19381,-18.87987 6.34557,-1.17304 13.27595,1.90201 19.63571,8.71248 3.26101,3.49213 5.29716,5.86329 5.97074,6.95317 0.30182,0.48837 -24.65978,25.29287 -31.24243,31.04583 -0.78894,0.68949 -3.78325,3.65519 -6.65402,6.59043 -2.87076,2.93524 -5.57284,5.33681 -6.00461,5.33681 -0.43178,0 -0.78504,0.36044 -0.78504,0.801 0,1.38644 -9.0153,9.48307 -13.42981,12.06127 -6.06808,3.54396 -12.21992,4.31962 -19.96171,2.51692 z"
                                 id="path4" />
                            <path style="fill:#1693df;fill-opacity:1;stroke:#1692df;stroke-width:0.585817;stroke-opacity:1"
                                 d="m 108.27635,148.0328 c -0.15548,-0.15547 -1.24461,-0.40584 -2.42029,-0.55636 -1.17568,-0.15052 -3.49291,-1.18641 -5.14941,-2.30197 -5.243915,-3.53144 -31.83672,-29.14604 -33.230335,-32.00795 -2.070848,-4.25267 -1.606782,-10.94583 1.009516,-14.560075 2.245783,-3.102414 30.58894,-30.877777 32.904299,-32.245142 3.25168,-1.920318 9.59683,-2.05089 13.41761,-0.276107 2.78378,1.29309 3.47769,1.899211 9.08016,7.931475 15.87635,17.094288 19.49786,19.874595 28.18127,21.635409 4.33812,0.879682 10.2743,0.0808 15.01604,-2.020858 4.55209,-2.017593 11.29779,-7.73935 17.13434,-14.533492 7.40162,-8.616007 8.60502,-12.185526 5.88282,-17.449671 -1.20162,-2.323682 -6.80741,-7.582709 -13.19811,-12.381696 -3.06089,-2.298523 -5.65312,-4.414712 -5.76052,-4.702642 -0.1074,-0.28793 -0.50283,-0.524413 -0.87872,-0.525519 -0.37591,-0.0011 -2.72073,-1.862494 -5.21072,-4.136419 -5.32088,-4.859169 -6.377,-7.149167 -5.18342,-11.239245 0.59611,-2.042705 2.48719,-4.239058 10.53271,-12.232953 10.13703,-10.0719818 12.37109,-11.7562207 15.59406,-11.7562207 3.36045,0 3.46971,0.089492 15.35095,12.5728557 5.90436,6.203578 11.51579,8.071317 18.06267,6.012071 3.95378,-1.243611 7.78902,-4.308298 14.2396,-11.37864 5.39074,-5.9086696 7.22734,-7.1480478 10.66279,-7.1954608 2.75962,-0.038089 4.59288,1.0618119 8.3778,5.0263594 1.5692,1.6436734 5.29736,5.4928614 8.2848,8.5537504 8.26065,8.463721 9.59363,11.072566 7.9143,15.489511 -0.65512,1.7231 -8.08571,9.482695 -8.86855,9.261223 -0.28472,-0.08055 -0.51767,0.218653 -0.51767,0.664895 0,0.446243 -0.59313,1.219013 -1.31808,1.717265 -0.72495,0.498254 -3.55884,3.036209 -6.29753,5.639899 -5.37873,5.113586 -7.8137,6.314006 -11.43102,5.635392 -2.04008,-0.382722 -5.23715,-3.213205 -12.80394,-11.335818 -0.68642,-0.736843 -2.63744,-2.300184 -4.3356,-3.474089 -2.81831,-1.948259 -3.51258,-2.156682 -7.96203,-2.390219 -6.36665,-0.334164 -10.1569,0.775422 -14.57987,4.268214 -3.17592,2.508003 -3.36844,2.805543 -3.36844,5.205895 0,3.179365 1.60708,5.656427 6.57867,10.139996 2.02021,1.821898 11.59652,11.242303 21.28069,20.934242 16.5439,16.557161 17.66792,17.816322 18.60646,20.843674 1.10073,3.55057 0.91139,7.48413 -0.53414,11.09688 -0.8283,2.07008 -8.896,10.34131 -25.58855,26.23406 -6.1604,5.86521 -7.28704,6.68721 -10.5447,7.69338 -2.83649,0.87607 -4.31856,1.02042 -6.66647,0.64925 -5.27763,-0.83431 -6.04721,-1.41624 -15.4341,-11.67056 -7.29117,-7.96494 -8.68595,-9.2088 -13.67331,-12.1938 -5.45095,-3.26247 -7.71407,-3.87451 -14.44587,-3.90677 -12.26902,-0.0588 -17.81733,3.38905 -33.70121,20.94262 -4.99063,5.51523 -6.51389,6.5859 -11.11373,7.81163 -3.30036,0.87946 -3.50112,0.90583 -3.89522,0.51173 z"
                                 id="path5" />
                        </g>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight"><?php echo $t['title']; ?></h1>
                    <p class="text-sm text-slate-400"><?php echo $t['subtitle']; ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                <!-- Nyelvválasztó gombok (HU, EN, ZH) -->
                <div class="flex items-center bg-slate-800 p-1 rounded-xl border border-slate-700">
                    <button onclick="changeLanguage('hu')" title="Magyar" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition <?php echo $lang === 'hu' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-slate-200'; ?>">
                        <span>HU</span>
                    </button>
                    <button onclick="changeLanguage('en')" title="English" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition <?php echo $lang === 'en' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-slate-200'; ?>">
                        <span>EN</span>
                    </button>
                    <button onclick="changeLanguage('zh')" title="中文" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition <?php echo $lang === 'zh' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-slate-200'; ?>">
                        <span>ZH</span>
                    </button>
                </div>
                <button onclick="loadData(); loadLogs();" class="bg-slate-700 hover:bg-slate-600 text-slate-200 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 shadow">
                    <i class="fa-solid fa-rotate-right" id="refresh-icon"></i> <?php echo $t['refresh']; ?>
                </button>
                <button onclick="openModal('banModal')" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 shadow shadow-blue-600/30">
                    <i class="fa-solid fa-ban"></i> <?php echo $t['new_ban']; ?>
                </button>
            </div>
        </header>

        <!-- Óránkénti oszlopdiagram (Gördülő 24 óra) -->
        <div class="bg-darkcard p-5 rounded-2xl border border-slate-800 shadow mb-6">
            <h2 class="font-semibold text-sm flex items-center gap-2 text-slate-300 mb-4">
                <i class="fa-solid fa-chart-column text-blue-400"></i> <?php echo $t['chart_hourly_title']; ?>
            </h2>
            <div class="w-full h-64 relative">
                <canvas id="hourlyBarChart"></canvas>
            </div>
        </div>

        <!-- Státusz visszajelzés -->
        <div id="alertBox" class="hidden mb-6 p-4 rounded-xl text-sm font-medium shadow-md transition-all"></div>

        <!-- Fő tartalom -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-darkcard p-4 rounded-2xl border border-slate-800 shadow flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="searchInput" onkeyup="filterData()" placeholder="<?php echo $t['search_placeholder']; ?>" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2 text-sm focus:outline-none focus:border-blue-500 text-slate-200">
                    </div>
                    <div class="text-sm text-slate-400" id="jailStats">...</div>
                </div>

                <div id="jailsContainer" class="space-y-4">
                    <div class="bg-darkcard p-8 rounded-2xl border border-slate-800 text-center text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
                        <p><?php echo $t['loading']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Jobb oldal: Tortadiagram + Élő Log -->
            <div class="space-y-6">
                <div class="bg-darkcard rounded-2xl border border-slate-800 shadow p-4 flex flex-col items-center">
                    <h2 class="font-semibold text-sm flex items-center gap-2 text-slate-300 mb-3 self-start">
                        <i class="fa-solid fa-chart-pie text-blue-400"></i> <?php echo $t['chart_pie_title']; ?>
                    </h2>
                    <div class="w-full h-48 relative flex items-center justify-center">
                        <canvas id="bannedChart"></canvas>
                    </div>
                </div>

                <div class="bg-darkcard rounded-2xl border border-slate-800 shadow overflow-hidden flex flex-col h-[380px]">
                    <div class="p-4 bg-slate-900/60 border-b border-slate-800 flex justify-between items-center">
                        <h2 class="font-semibold text-sm flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-terminal text-blue-400"></i> <?php echo $t['live_logs']; ?>
                        </h2>
                        <span class="text-xs bg-slate-800 text-slate-400 px-2.5 py-1 rounded-lg">/var/log/fail2ban.log</span>
                    </div>
                    <div id="logContainer" class="p-4 bg-slate-950 font-mono text-xs overflow-y-auto flex-1 text-slate-300 space-y-1">
                        <p class="text-slate-500">...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LÁBLÉC -->
    <footer class="w-full bg-darkcard border-t border-slate-800 py-4 px-6 mt-10">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 gap-2">
            <div>Ágoston Lajos, 2026</div>
            <div><?php echo $t['footer_version'].$version; ?></div>
        </div>
    </footer>

    <!-- Modal -->
    <div id="banModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-darkcard border border-slate-700 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold flex items-center gap-2 text-slate-100">
                    <i class="fa-solid fa-ban text-red-500"></i> <?php echo $t['modal_title']; ?>
                </h3>
                <button onclick="closeModal('banModal')" class="text-slate-400 hover:text-slate-200"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="banForm" onsubmit="handleBan(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1"><?php echo $t['target_jail']; ?></label>
                    <select id="banJailSelect" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-blue-500"></select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1"><?php echo $t['ip_label']; ?></label>
                    <input type="text" id="banIpInput" required placeholder="<?php echo $t['ip_placeholder']; ?>" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('banModal')" class="px-4 py-2 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition"><?php echo $t['cancel']; ?></button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-500 text-white transition shadow shadow-red-600/30"><?php echo $t['execute_ban']; ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Logika -->
    <script>
        let jailDataGlobal = [];
        const currentLang = '<?php echo $lang; ?>';
        let bannedChartInstance = null;
        let hourlyChartInstance = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            loadLogs();
            setInterval(() => { loadData(); loadLogs(); }, 30000);
        });

        function changeLanguage(lang) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', lang);
            window.location.href = url.toString();
        }

        function showAlert(message, type = 'success') {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = `mb-6 p-4 rounded-xl text-sm font-medium shadow-md transition-all ${
                type === 'success' ? 'bg-emerald-900/50 border border-emerald-700 text-emerald-200' : 'bg-red-900/50 border border-red-700 text-red-200'
            }`;
            alertBox.innerHTML = `<div class="flex items-center gap-2"><i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i><span>${message}</span></div>`;
            alertBox.classList.remove('hidden');
            setTimeout(() => alertBox.classList.add('hidden'), 5000);
        }

        async function loadData() {
            const refreshIcon = document.getElementById('refresh-icon');
            refreshIcon.classList.add('fa-spin');
            try {
                const response = await fetch(`api.php?action=status&lang=${currentLang}`);
                const text = await response.text();
                if (!text || text.trim() === "") throw new Error("Üres válasz");

                const result = JSON.parse(text);
                if (result.status === 'success') {
                    jailDataGlobal = result.jails;
                    renderJails(jailDataGlobal);
                    updateJailSelectOptions(jailDataGlobal);
                    renderPieChart(jailDataGlobal);
                    renderHourlyChart(result.hourly_stats);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showAlert(error.message, 'error');
            } finally {
                refreshIcon.classList.remove('fa-spin');
            }
        }

        async function loadLogs() {
            try {
                const response = await fetch(`api.php?action=logs&lang=${currentLang}`);
                const result = await response.json();
                if (result.status === 'success') {
                    const logContainer = document.getElementById('logContainer');
                    logContainer.innerHTML = '';
                    result.logs.forEach(line => {
                        const p = document.createElement('p');
                        p.textContent = line;
                        p.className = line.includes('NOTICE') || line.includes('Ban') ? 'text-amber-400' : (line.includes('ERROR') ? 'text-red-400' : (line.includes('Unban') ? 'text-emerald-400' : 'text-slate-400'));
                        logContainer.appendChild(p);
                    });
                    logContainer.scrollTop = logContainer.scrollHeight;
                }
            } catch (e) { console.error(e); }
        }

        function renderPieChart(jails) {
            const ctx = document.getElementById('bannedChart').getContext('2d');
            const labels = jails.map(j => j.name);
            const data = jails.map(j => j.banned_count);
            const colors = ['rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)'];

            if (bannedChartInstance) {
                bannedChartInstance.data.labels = labels;
                bannedChartInstance.data.datasets[0].data = data;
                bannedChartInstance.update();
                return;
            }

            bannedChartInstance = new Chart(ctx, {
                type: 'pie',
                data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 1, borderColor: '#1e293b' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12 } } } }
            });
        }

        function renderHourlyChart(hourlyStats) {
            const ctx = document.getElementById('hourlyBarChart').getContext('2d');
            const hours = Object.keys(hourlyStats);
            
            let jailNames = new Set();
            Object.values(hourlyStats).forEach(hourData => {
                Object.keys(hourData).forEach(jName => jailNames.add(jName));
            });
            const jailsArray = Array.from(jailNames);

            const colorPalette = [
                'rgba(59, 130, 246, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ];

            let datasets = jailsArray.map((jailName, index) => {
                let dataPoints = hours.map(hour => {
                    return hourlyStats[hour][jailName] || 0;
                });
                return {
                    label: jailName,
                    data: dataPoints,
                    backgroundColor: colorPalette[index % colorPalette.length],
                    borderWidth: 1,
                    borderRadius: 4
                };
            });

            if (hourlyChartInstance) {
                hourlyChartInstance.data.labels = hours;
                hourlyChartInstance.data.datasets = datasets;
                hourlyChartInstance.update();
                return;
            }

            hourlyChartInstance = new Chart(ctx, {
                type: 'bar',
                data: { labels: hours, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { size: 10 } }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', precision: 0 }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12 }
                        }
                    }
                }
            });
        }

        function renderJails(jails) {
            const container = document.getElementById('jailsContainer');
            const stats = document.getElementById('jailStats');
            let totalBanned = jails.reduce((acc, j) => acc + j.banned_count, 0);
            
            const statsTemplate = "<?php echo $t['total_jails']; ?>";
            stats.textContent = statsTemplate.replace('%d', jails.length).replace('%d', totalBanned);

            if (jails.length === 0) {
                container.innerHTML = `<div class="bg-darkcard p-8 rounded-2xl border border-slate-800 text-center text-slate-400"><?php echo $t['no_banned_ips']; ?></div>`;
                return;
            }

            container.innerHTML = '';
            jails.forEach(jail => {
                let badgeClass = jail.banned_count > 0 ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-slate-800 text-slate-400 border-slate-700';
                let ipListHtml = '';
                
                if (jail.banned_ips.length > 0) {
                    ipListHtml = `<div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-900/80 text-slate-400 uppercase text-xs"><tr><th class="px-4 py-2.5 rounded-l-xl"><?php echo $t['banned_ips_title']; ?></th><th class="px-4 py-2.5 text-right rounded-r-xl"><?php echo $t['action']; ?></th></tr></thead><tbody class="divide-y divide-slate-800/60">`;
                    jail.banned_ips.forEach(item => {
                        ipListHtml += `<tr class="hover:bg-slate-800/40 transition"><td class="px-4 py-3 font-mono text-slate-200">${item.ip} <span class="text-xs text-slate-500 font-sans">(${item.time})</span></td><td class="px-4 py-3 text-right"><button onclick="unbanIp('${jail.name}', '${item.ip}')" class="bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-medium transition border border-emerald-500/30 flex items-center gap-1.5 ml-auto"><i class="fa-solid fa-unlock"></i> <?php echo $t['unban']; ?></button></td></tr>`;
                    });
                    ipListHtml += `</tbody></table></div>`;
                } else {
                    ipListHtml = `<p class="text-xs text-slate-500 mt-3 italic"><?php echo $t['no_banned_ips']; ?></p>`;
                }

                const card = document.createElement('div');
                card.className = 'bg-darkcard border border-slate-800 rounded-2xl p-5 shadow';
                card.innerHTML = `<div class="flex justify-between items-center"><div class="flex items-center gap-3"><div class="bg-slate-800 p-2.5 rounded-xl text-blue-400"><i class="fa-solid fa-shield"></i></div><div><h3 class="font-bold text-base text-slate-100">${jail.name}</h3><p class="text-xs text-slate-400"><?php echo $t['active_protection']; ?></p></div></div><span class="px-3 py-1 rounded-full text-xs font-semibold border ${badgeClass}">${jail.banned_count}</span></div>${ipListHtml}`;
                container.appendChild(card);
            });
        }

        function updateJailSelectOptions(jails) {
            const select = document.getElementById('banJailSelect');
            select.innerHTML = '';
            jails.forEach(j => {
                const opt = document.createElement('option');
                opt.value = j.name;
                opt.textContent = j.name;
                select.appendChild(opt);
            });
        }

        function filterData() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const filtered = jailDataGlobal.map(jail => {
                const matchedIps = jail.banned_ips.filter(item => item.ip.toLowerCase().includes(query));
                if (jail.name.toLowerCase().includes(query)) return jail;
                return { ...jail, banned_ips: matchedIps, banned_count: matchedIps.length };
            }).filter(jail => jail.name.toLowerCase().includes(query) || jail.banned_ips.length > 0);
            renderJails(filtered);
        }

        async function handleBan(event) {
            event.preventDefault();
            const formData = new FormData();
            formData.append('action', 'ban');
            formData.append('lang', currentLang);
            formData.append('jail', document.getElementById('banJailSelect').value);
            formData.append('ip', document.getElementById('banIpInput').value);

            const res = await (await fetch('api.php', { method: 'POST', body: formData })).json();
            if (res.status === 'success') {
                showAlert(res.message);
                closeModal('banModal');
                document.getElementById('banIpInput').value = '';
                loadData(); loadLogs();
            } else { showAlert(res.message, 'error'); }
        }

        async function unbanIp(jail, ip) {
            const confirmMsg = "<?php echo $t['confirm_unban']; ?>".replace('%s', ip);
            if (!confirm(confirmMsg)) return;
            const formData = new FormData();
            formData.append('action', 'unban');
            formData.append('lang', currentLang);
            formData.append('jail', jail);
            formData.append('ip', ip);

            const res = await (await fetch('api.php', { method: 'POST', body: formData })).json();
            if (res.status === 'success') {
                showAlert(res.message);
                loadData(); loadLogs();
            } else { showAlert(res.message, 'error'); }
        }

        function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
    </script>
</body>
</html>