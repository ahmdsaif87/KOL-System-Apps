<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .perf-container {
        font-family: 'Inter', sans-serif;
        padding: 20px;
    }
    .perf-header {
        background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        margin-bottom: 30px;
    }
    .perf-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        margin-right: 25px;
    }
    .perf-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
    }
    .perf-subtitle {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }
    .tier-badge {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
        backdrop-filter: blur(5px);
    }
    
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #E2E8F0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }
    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .platform-icon {
        font-size: 24px;
        margin-right: 10px;
    }
    .platform-name {
        font-size: 18px;
        font-weight: 600;
        color: #0F172A;
    }
    
    .stat-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #F1F5F9;
    }
    .stat-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .stat-label {
        color: #64748B;
        font-size: 14px;
        font-weight: 500;
    }
    .stat-value {
        color: #0F172A;
        font-size: 15px;
        font-weight: 700;
    }
    .stat-value.highlight-er {
        color: #059669;
        font-size: 18px;
    }
    
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #E2E8F0;
    }
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 20px;
        border-bottom: 2px solid #F1F5F9;
        padding-bottom: 10px;
    }
    .sync-note {
        font-size: 12px;
        color: #94A3B8;
        text-align: right;
        margin-top: 10px;
    }
</style>

<div class="row" style="margin-bottom: 20px; padding: 0 20px;">
    <div class="col-md-12 text-right">
        <a href="<?= site_url('administrator/kols') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to List</a>
        <button id="btn-export" class="btn btn-primary"><i class="fa fa-camera"></i> Export to Image</button>
    </div>
</div>

<div class="perf-container" id="perf-capture-area">
    <div class="perf-header">
        <?php 
        $avatar = base_url('asset/img/avatar.png');
        if (!empty($kol->avatar_url)) {
            if (preg_match('/^https?:\/\//i', $kol->avatar_url)) {
                $avatar = base_url('avatar_proxy.php?url=' . urlencode($kol->avatar_url));
            } else {
                $avatar = base_url('uploads/kols/' . $kol->avatar_url);
            }
        }
        ?>
        <img src="<?= $avatar ?>" class="perf-avatar" onerror="this.src='<?= base_url('asset/img/avatar.png') ?>'" referrerpolicy="no-referrer">
        <div>
            <h1 class="perf-title"><?= htmlspecialchars($kol->name) ?></h1>
            <p class="perf-subtitle"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($kol->city) ?>, <?= htmlspecialchars($kol->province) ?> | <i class="fa fa-envelope"></i> <?= htmlspecialchars($kol->email) ?></p>
            <div class="tier-badge"><?= htmlspecialchars($kol->tier_name) ?></div>
        </div>
    </div>

    <?php if(empty($platforms)): ?>
        <div class="alert alert-warning">
            <h4><i class="icon fa fa-warning"></i> No Platforms Configured!</h4>
            This KOL does not have any active platforms attached to their profile.
        </div>
    <?php else: ?>
        <div class="grid-container">
            <?php foreach($platforms as $p): ?>
            <div class="stat-card">
                <div class="stat-header">
                    <?php 
                        $iconClass = 'fa-globe';
                        if($p->code == 'TIKTOK') $iconClass = 'fa-music';
                        if($p->code == 'INSTAGRAM') $iconClass = 'fa-instagram';
                        if($p->code == 'YOUTUBE') $iconClass = 'fa-youtube-play';
                    ?>
                    <i class="fa <?= $iconClass ?> platform-icon" style="color: #4F46E5;"></i>
                    <span class="platform-name"><?= htmlspecialchars($p->platform_name) ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Username</span>
                    <?php $p_url = get_kol_profile_url(isset($p->profile_url) ? $p->profile_url : '', $p->username, isset($p->code) ? $p->code : '', isset($p->platform_name) ? $p->platform_name : ''); ?>
                    <span class="stat-value"><a href="<?= _ent($p_url) ?>" target="_blank" style="color: inherit; text-decoration: underline;">@<?= htmlspecialchars($p->username) ?></a></span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Followers</span>
                    <span class="stat-value"><?= number_format($p->followers) ?></span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Avg Views (30 posts)</span>
                    <span class="stat-value"><?= number_format($p->avg_views) ?></span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Avg ER (30 posts)</span>
                    <span class="stat-value highlight-er"><?= number_format($p->avg_er * 100, 2) ?>%</span>
                </div>
                
                <div class="sync-note">
                    Last Synced: <?= $p->last_synced_at ? date('d M Y, H:i', strtotime($p->last_synced_at)) : 'Never' ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="chart-card">
            <h3 class="chart-title"><i class="fa fa-line-chart"></i> Growth Trend (Average Views)</h3>
            <canvas id="growthChart" height="100"></canvas>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Export functionality
    document.getElementById('btn-export').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Exporting...';
        btn.disabled = true;

        const target = document.getElementById('perf-capture-area');
        
        html2canvas(target, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#F8FAFC'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'KOL_Performance_<?= htmlspecialchars(str_replace(' ', '_', $kol->name)) ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            alert('Failed to export image.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    <?php if(!empty($platforms)): ?>
    const labels = <?= $chart_labels ?>;
    const datasetsRaw = <?= $chart_data ?>;
    
    const colors = {
        'TIKTOK': '#000000',
        'INSTAGRAM': '#E1306C',
        'YOUTUBE': '#FF0000'
    };

    const datasets = Object.keys(datasetsRaw).map(platform => {
        return {
            label: platform,
            data: datasetsRaw[platform],
            borderColor: colors[platform] || '#4F46E5',
            backgroundColor: (colors[platform] || '#4F46E5') + '20',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        };
    });

    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    <?php endif; ?>
});
</script>
