<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'admin') {
    die("Доступ запрещен: недостаточно прав");
}

try {
    // Проверка существования таблиц
    $tables = ['orders'];
    foreach ($tables as $table) {
        $exists = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount();
        if ($exists === 0) {
            throw new Exception("Таблица $table не существует");
        }
    }

    // Статистика по категориям
    $stats = $pdo->query("
        SELECT 
            category, 
            COUNT(id) as sales_count, 
            IFNULL(SUM(total_amount), 0) as sales_total,
            IFNULL(AVG(total_amount), 0) as avg_amount
        FROM orders
        WHERE status = 'completed'
        GROUP BY category
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Динамика продаж за последние 30 дней
    $dateStats = $pdo->query("
        SELECT 
            DATE(created_at) as day,
            COUNT(id) as orders_count,
            SUM(quantity * price) as daily_revenue
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND status = 'completed'
        GROUP BY day
        ORDER BY day ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Расширенная статистика</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
    <style>
        :root {
            --primary-purple: #AA00FF;
            --primary-green: #00E676;
            --glass-white: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-white: rgba(255, 255, 255, 0.9);
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, var(--primary-purple), var(--primary-green));
            color: var(--text-white);
            min-height: 100vh;
        }
        .admin-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--glass-white);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }
        .admin-title {
            font-size: 2rem;
            margin: 0;
        }
        .admin-nav {
            display: flex;
            gap: 1rem;
        }
        .admin-nav a {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-nav a:hover {
            background: var(--primary-purple);
        }
        .stats-section {
            margin-bottom: 3rem;
        }
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            position: relative;
            padding-bottom: 0.5rem;
        }
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-purple), var(--primary-green));
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }
        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }
        .chart-title {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }
        .stats-table th,
        .stats-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        .stats-table th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        .stats-table tr:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        @media (max-width: 768px) {
            .admin-container {
                margin: 1rem;
                padding: 1rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Расширенная статистика</h1>
            <div class="admin-nav">
                <a href="admin.php">Управление</a>
                <a href="admin_stats.php" class="active">Статистика</a>
                <a href="logout.php">Выйти</a>
            </div>
        </div>
        <section class="stats-section">
            <h2 class="section-title">Динамика продаж за последние 30 дней</h2>
            <div class="stats-grid">
                <div class="chart-container">
                    <h3 class="chart-title">Количество заказов по дням</h3>
                    <canvas id="ordersChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Выручка по дням</h3>
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
        </section>
        <section class="stats-section">
            <h2 class="section-title">Статистика по категориям</h2>
            <div class="stats-grid">
                <div class="chart-container">
                    <h3 class="chart-title">Количество проданных товаров</h3>
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Выручка по категориям</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </section>
    </div>

    <!-- Передача данных в JavaScript -->
    <script>
        const categories = <?= json_encode(array_column($stats, 'category')) ?>;
        const salesData = <?= json_encode(array_column($stats, 'sales_count')) ?>;
        const revenueData = <?= json_encode(array_column($stats, 'sales_total')) ?>;

        const dates = <?= json_encode(array_column($dateStats, 'day')) ?>;
        const ordersCount = <?= json_encode(array_column($dateStats, 'orders_count')) ?>;
        const dailyRevenue = <?= json_encode(array_column($dateStats, 'daily_revenue')) ?>;

        // График количества продаж
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Продано товаров',
                    data: salesData,
                    backgroundColor: [
                        'rgba(170, 0, 255, 0.7)',
                        'rgba(0, 230, 118, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(233, 30, 99, 0.7)'
                    ],
                    borderColor: [
                        'rgba(170, 0, 255, 1)',
                        'rgba(0, 230, 118, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(233, 30, 99, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // График выручки
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Выручка (₽)',
                    data: revenueData,
                    backgroundColor: [
                        'rgba(170, 0, 255, 0.7)',
                        'rgba(0, 230, 118, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(233, 30, 99, 0.7)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        }
                    }
                }
            }
        });

        // График количества заказов по дням
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Заказов',
                    data: ordersCount,
                    backgroundColor: 'rgba(170, 0, 255, 0.2)',
                    borderColor: 'rgba(170, 0, 255, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // График выручки по дням
        const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(dailyRevenueCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Выручка (₽)',
                    data: dailyRevenue,
                    backgroundColor: 'rgba(0, 230, 118, 0.2)',
                    borderColor: 'rgba(0, 230, 118, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            callback: function(value) {
                                return value.toLocaleString('ru-RU') + ' ₽';
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.8)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>