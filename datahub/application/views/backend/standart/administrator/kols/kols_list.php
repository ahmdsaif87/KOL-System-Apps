<link rel="stylesheet" href="<?= BASE_ASSET; ?>/admin-lte/plugins/datatables/dataTables.bootstrap.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="content-header">
    <h1>KOLs <small>Performance List</small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">KOLs</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border" style="display:flex; justify-content: space-between; align-items:center;">
                    <h3 class="box-title">KOL Data Hub</h3>
                    <button id="btn-bulk-scrape" class="btn btn-warning"><i class="fa fa-refresh"></i> Bulk Scrape Selected</button>
                </div>
                <div class="box-body">
                    <table id="kol-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 30px;"><input type="checkbox" id="check-all"></th>
                                <th>Name</th>
                                <th>Tier</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kols as $kol): ?>
                            <tr>
                                <td><input type="checkbox" class="kol-checkbox" value="<?= $kol->id ?>"></td>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <img src="<?= $kol->avatar_url ? $kol->avatar_url : base_url('asset/img/avatar.png') ?>" style="width:40px;height:40px;border-radius:50%;margin-right:10px;object-fit:cover;">
                                        <strong><?= htmlspecialchars($kol->name) ?></strong>
                                    </div>
                                </td>
                                <td><span class="label label-info"><?= htmlspecialchars($kol->tier_name) ?></span></td>
                                <td><?= htmlspecialchars($kol->city) ?>, <?= htmlspecialchars($kol->province) ?></td>
                                <td>
                                    <?php if($kol->status == 'ACTIVE'): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-warning"><?= htmlspecialchars($kol->status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('administrator/kols/view/' . $kol->id) ?>" class="btn btn-sm btn-primary">
                                        <i class="fa fa-line-chart"></i> View Performance
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_ASSET; ?>/admin-lte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    $(function () {
        $('#kol-table').DataTable({
            "order": [[ 1, "asc" ]] // Order by name, ignoring checkbox column
        });

        $('#check-all').on('click', function() {
            $('.kol-checkbox').prop('checked', this.checked);
        });

        $('#btn-bulk-scrape').on('click', function() {
            let selected = [];
            $('.kol-checkbox:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                Swal.fire('Oops', 'Please select at least one KOL to scrape.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Scraping in progress...',
                html: 'Connecting to Apify. This may take a while depending on the number of KOLs.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= site_url("administrator/scraping/bulk") ?>',
                type: 'POST',
                data: {
                    kol_ids: selected,
                    csrf_test_name: '<?= $this->security->get_csrf_hash() ?>' // Assuming CI CSRF is on
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Completed!', res.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Failed', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'A server error occurred.', 'error');
                }
            });
        });
    });
</script>
