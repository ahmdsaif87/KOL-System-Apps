<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function domo(){
    $('*').bind('keydown', 'Ctrl+e', function assets() {
        $('#btn_edit').trigger('click');
        return false;
    });

    $('*').bind('keydown', 'Ctrl+x', function assets() {
        $('#btn_back').trigger('click');
        return false;
    });
}
jQuery(document).ready(domo);
</script>

<style>
.kpi-card {
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    color: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s ease;
}
.kpi-card:hover { transform: translateY(-2px); }
.kpi-card .kpi-val { font-size: 24px; font-weight: 700; margin: 0; line-height: 1.2; }
.kpi-card .kpi-label { font-size: 12px; opacity: 0.9; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.kpi-card .kpi-icon { font-size: 38px; opacity: 0.35; }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= cclang('brands'); ?> <small><?= cclang('detail', ['Brands']); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/brands'); ?>"><?= cclang('brands'); ?></a></li>
        <li class="active"><?= cclang('detail'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- Brand Overview Box -->
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10); margin-bottom: 20px;">
                <div class="box-body" style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <?php 
                                $logo_src = !empty($brands->logo) && file_exists(FCPATH . $brands->logo) 
                                    ? BASE_URL . $brands->logo 
                                    : BASE_URL . 'asset/logo.png';
                            ?>
                            <img src="<?= $logo_src; ?>" alt="<?= _ent($brands->name); ?>" class="img-thumbnail" style="max-height: 55px; max-width: 80px; object-fit: contain; background: #fff; padding: 4px;">
                            <div>
                                <h3 style="margin: 0; color: #172b4d; font-size: 22px; font-weight: 700;">
                                    <?= _ent($brands->name); ?>
                                    <span class="label label-primary" style="font-family: monospace; font-size: 12px; margin-left: 6px;"><?= _ent($brands->code); ?></span>
                                    <?php if ($brands->is_active): ?>
                                        <span class="label label-success" style="font-size: 11px;"><i class="fa fa-check-circle"></i> Active</span>
                                    <?php else: ?>
                                        <span class="label label-danger" style="font-size: 11px;"><i class="fa fa-ban"></i> Inactive</span>
                                    <?php endif; ?>
                                </h3>
                                <div style="margin-top: 4px; color: #6b778c; font-size: 13px;">
                                    <?= _ent($brands->company_name ?: $brands->legal_entity_name ?: 'Entitas Brand'); ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" class="btn btn-success btn-flat btn-switch-context" data-brand-id="<?= $brands->id; ?>" title="Beralih ke konteks brand ini">
                                <i class="fa fa-exchange"></i> Beralih ke Brand Ini
                            </button>
                            <?php is_allowed('brands_update', function() use ($brands){ ?>
                                <a href="<?= site_url('administrator/brands/edit/' . $brands->id); ?>" class="btn btn-primary btn-flat" id="btn_edit" title="Edit Brand (Ctrl+e)">
                                    <i class="fa fa-pencil"></i> Edit Brand
                                </a>
                            <?php }) ?>
                            <a href="<?= site_url('administrator/brands'); ?>" class="btn btn-default btn-flat" id="btn_back" title="Kembali ke Daftar (Ctrl+x)">
                                <i class="fa fa-undo"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <!-- 4 KPI Summary Widgets -->
                    <?php
                        $active_campaigns_count = isset($kpis->active_campaigns) ? (int)$kpis->active_campaigns : (isset($metrics['campaigns_count']) ? (int)$metrics['campaigns_count'] : 0);
                        $total_budget_val = isset($kpis->total_budget) ? (float)$kpis->total_budget : (isset($metrics['total_budget']) ? (float)$metrics['total_budget'] : 0.0);
                        $total_products_count = isset($kpis->total_products) ? (int)$kpis->total_products : (isset($metrics['products_count']) ? (int)$metrics['products_count'] : 0);
                        $total_team_count = !empty($team_members) ? count($team_members) : (isset($kpis->total_team_members) ? (int)$kpis->total_team_members : 0);
                    ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="kpi-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                                <div>
                                    <h4 class="kpi-val"><?= $active_campaigns_count; ?></h4>
                                    <div class="kpi-label">Active Campaigns</div>
                                </div>
                                <div class="kpi-icon"><i class="fa fa-bullhorn"></i></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="kpi-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <div>
                                    <h4 class="kpi-val">Rp <?= number_format($total_budget_val, 0, ',', '.'); ?></h4>
                                    <div class="kpi-label">Total Budget Campaign</div>
                                </div>
                                <div class="kpi-icon"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="kpi-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <div>
                                    <h4 class="kpi-val"><?= $total_products_count; ?></h4>
                                    <div class="kpi-label">Total Produk Katalog</div>
                                </div>
                                <div class="kpi-icon"><i class="fa fa-cubes"></i></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="kpi-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                <div>
                                    <h4 class="kpi-val" id="kpi_team_count"><?= $total_team_count; ?></h4>
                                    <div class="kpi-label">Anggota Tim Brand</div>
                                </div>
                                <div class="kpi-icon"><i class="fa fa-users"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details Table -->
                    <div class="table-responsive" style="margin-top: 10px;">
                        <table class="table table-bordered table-striped" style="border: 1px solid #e2e8f0; border-radius: 6px;">
                            <tbody>
                                <tr>
                                    <th width="240" style="background: #f8fafc; color: #475569;">Nama Brand</th>
                                    <td><strong><?= _ent($brands->name); ?></strong></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Kode Brand</th>
                                    <td><code><?= _ent($brands->code); ?></code></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Badan Hukum / PT</th>
                                    <td><?= _ent($brands->company_name ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Nama Legal Entity</th>
                                    <td><?= _ent($brands->legal_entity_name ?: $brands->company_name ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Penanggung Jawab (PIC)</th>
                                    <td><?= _ent($brands->pic_name ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Email Resmi</th>
                                    <td><?= !empty($brands->email) ? '<a href="mailto:' . _ent($brands->email) . '">' . _ent($brands->email) . '</a>' : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Telepon / WhatsApp</th>
                                    <td><?= _ent($brands->phone ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Website</th>
                                    <td><?= !empty($brands->website) ? '<a href="' . _ent(prep_url($brands->website)) . '" target="_blank" rel="noopener noreferrer">' . _ent($brands->website) . ' <i class="fa fa-external-link"></i></a>' : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Alamat Kantor</th>
                                    <td><?= nl2br(_ent($brands->address ?: '-')); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Deskripsi Brand</th>
                                    <td><?= nl2br(_ent($brands->description ?: '-')); ?></td>
                                </tr>
                                <tr>
                                    <th style="background: #f8fafc; color: #475569;">Terdaftar Pada</th>
                                    <td><?= _ent($brands->created_at); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Team Members Management Box -->
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px; flex-wrap: wrap;">
                        <div>
                            <h3 style="margin: 0; color: #172b4d; font-size: 18px; font-weight: 700;">
                                <i class="fa fa-users text-primary"></i> Tim & Anggota Brand
                            </h3>
                            <div style="margin-top: 4px; color: #6b778c; font-size: 13px;">
                                Daftar staf dan pimpinan KOL yang terasosiasi langsung dengan <?= _ent($brands->name); ?>
                            </div>
                        </div>

                        <?php is_allowed('brands_update', function() { ?>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal_add_user" id="btn_modal_add_user">
                                <i class="fa fa-user-plus"></i> + Tambah User Baru ke Brand Ini
                            </button>
                            <button type="button" class="btn btn-default btn-flat" data-toggle="modal" data-target="#modal_assign_user" id="btn_modal_assign_user">
                                <i class="fa fa-link"></i> + Tugaskan Pengguna Eksisting
                            </button>
                        </div>
                        <?php }); ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" style="margin-bottom: 0; border: 1px solid #e7edf3; border-radius: 6px;">
                            <thead>
                                <tr style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase; letter-spacing: 0.02em;">
                                    <th style="border-bottom: 1px solid #e7edf3;">Nama Lengkap (Full Name)</th>
                                    <th style="border-bottom: 1px solid #e7edf3;">Username</th>
                                    <th style="border-bottom: 1px solid #e7edf3;">Email</th>
                                    <th style="border-bottom: 1px solid #e7edf3; text-align: center;">Jabatan / Role</th>
                                    <th style="border-bottom: 1px solid #e7edf3; text-align: center;">Last Login</th>
                                    <th style="border-bottom: 1px solid #e7edf3; text-align: center;">Status</th>
                                    <th style="border-bottom: 1px solid #e7edf3; text-align: center; width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_team_members">
                                <?php if (!empty($team_members)): ?>
                                    <?php foreach ($team_members as $member): ?>
                                    <tr id="row_member_<?= $member->id; ?>">
                                        <td style="vertical-align: middle;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <?php 
                                                    $avatar_url = !empty($member->avatar) && file_exists(FCPATH . 'uploads/user/' . $member->avatar)
                                                        ? BASE_URL . 'uploads/user/' . $member->avatar 
                                                        : BASE_URL . 'uploads/user/default.png';
                                                ?>
                                                <img src="<?= $avatar_url; ?>" class="img-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                                <strong><?= _ent($member->full_name); ?></strong>
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle;"><code><?= _ent($member->username); ?></code></td>
                                        <td style="vertical-align: middle;"><?= _ent($member->email); ?></td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?php 
                                                $role_display = !empty($member->group_name) ? $member->group_name : (!empty($member->role_name) ? $member->role_name : 'Staff');
                                            ?>
                                            <?php if (strpos($role_display, 'Leader KOL') !== false): ?>
                                                <span class="label label-primary" style="font-size: 11px;"><i class="fa fa-star"></i> Leader KOL</span>
                                            <?php elseif (strpos($role_display, 'Operational') !== false): ?>
                                                <span class="label label-info" style="font-size: 11px;"><i class="fa fa-cogs"></i> Operational</span>
                                            <?php else: ?>
                                                <span class="label label-default" style="font-size: 11px;"><?= _ent($role_display); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?= !empty($member->last_login) ? _ent($member->last_login) : '<span class="text-muted" style="font-size: 11px;">Belum pernah login</span>'; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?= $member->banned ? '<span class="label label-danger">Banned</span>' : '<span class="label label-success">Active</span>'; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <?php is_allowed('brands_update', function() use ($member) { ?>
                                            <button type="button" class="btn btn-xs btn-danger btn-flat btn-unassign-user" data-user-id="<?= $member->id; ?>" data-username="<?= _ent($member->username); ?>" title="Unassign dari brand ini">
                                                <i class="fa fa-user-times"></i> Unassign
                                            </button>
                                            <?php }); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr id="row_empty_team">
                                        <td colspan="7" class="text-center text-muted" style="padding: 24px;">
                                            <i class="fa fa-info-circle fa-2x" style="display: block; margin-bottom: 6px; color: #cbd5e1;"></i>
                                            Belum ada anggota tim yang ditugaskan pada brand ini. Gunakan tombol <strong>+ Tambah User Baru</strong> atau <strong>+ Tugaskan Pengguna Eksisting</strong> di atas.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: In-Page Add User -->
<div class="modal fade" id="modal_add_user" tabindex="-1" role="dialog" aria-labelledby="modalAddUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalAddUserLabel" style="font-weight: 700; color: #1e293b;">
                    <i class="fa fa-user-plus text-primary"></i> Tambah User Baru ke Brand Ini
                </h4>
            </div>
            <form id="form_add_user" class="form-horizontal">
                <input type="hidden" name="brand_id" value="<?= $brands->id; ?>">
                <div class="modal-body" style="padding: 20px;">
                    <div id="modal_add_user_alert" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="modal_full_name" class="col-sm-3 control-label">Nama Lengkap <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="full_name" id="modal_full_name" placeholder="Nama Lengkap Staf / Leader" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_username" class="col-sm-3 control-label">Username <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="username" id="modal_username" placeholder="Username (huruf/angka tanpa spasi)" required>
                            <small class="help-block text-muted">Digunakan untuk login ke sistem.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_email" class="col-sm-3 control-label">Email <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" id="modal_email" placeholder="user@perusahaan.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_password" class="col-sm-3 control-label">Password <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="password" id="modal_password" placeholder="Minimal 6 karakter" required>
                            <small class="help-block text-muted">Password minimal 6 karakter.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_group_id" class="col-sm-3 control-label">Jabatan / Role <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <select class="form-control" name="group_id" id="modal_group_id" required>
                                <option value="">-- Pilih Role / Jabatan --</option>
                                <?php 
                                    $role_groups = !empty($groups) ? $groups : (!empty($assignable_groups) ? $assignable_groups : []);
                                ?>
                                <?php if (!empty($role_groups)): ?>
                                    <?php foreach ($role_groups as $grp): ?>
                                        <option value="<?= $grp->id; ?>"><?= _ent($grp->name); ?> (<?= _ent($grp->definition); ?>)</option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="5">Operational (Operational)</option>
                                    <option value="6">Leader KOL (Leader KOL)</option>
                                <?php endif; ?>
                            </select>
                            <small class="help-block text-muted">Pilih apakah sebagai Leader KOL brand atau Staf Operational.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-flat" id="btn_save_user">
                        <i class="fa fa-save"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Assign Existing User -->
<div class="modal fade" id="modal_assign_user" tabindex="-1" role="dialog" aria-labelledby="modalAssignUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalAssignUserLabel" style="font-weight: 700; color: #1e293b;">
                    <i class="fa fa-link text-primary"></i> Tugaskan Pengguna Eksisting ke Brand
                </h4>
            </div>
            <form id="form_assign_user" class="form-horizontal">
                <input type="hidden" name="brand_id" value="<?= $brands->id; ?>">
                <div class="modal-body" style="padding: 20px;">
                    <div id="modal_assign_user_alert" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="assign_user_id" class="col-sm-3 control-label">Pilih User <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <select class="form-control" name="user_id" id="assign_user_id" required style="width: 100%;">
                                <option value="">-- Pilih User Tanpa Brand --</option>
                                <?php if (!empty($unassigned_users)): ?>
                                    <?php foreach ($unassigned_users as $u_un): ?>
                                        <option value="<?= $u_un->id; ?>">
                                            <?= _ent($u_un->full_name); ?> (<?= _ent($u_un->username); ?> - <?= _ent($u_un->email); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada user tanpa brand</option>
                                <?php endif; ?>
                            </select>
                            <small class="help-block text-muted">Hanya menampilkan pengguna yang saat ini belum memiliki afiliasi brand (`brand_id IS NULL`).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-flat" id="btn_save_assign_user">
                        <i class="fa fa-check"></i> Tugaskan ke Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Page script -->
<script>
$(document).ready(function() {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var currentBrandId = '<?= $brands->id; ?>';

    // 1. Submit In-Page User Creation Modal
    $('#form_add_user').submit(function(e) {
        e.preventDefault();
        $('#modal_add_user_alert').hide().html('');

        var formData = $(this).serializeArray();
        formData.push({ name: csrfName, value: csrfHash });

        var $btn = $('#btn_save_user');
        var originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: BASE_URL + 'administrator/brands/add_user',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(res) {
                if (res.success) {
                    $('#modal_add_user').modal('hide');
                    swal({
                        title: "Berhasil!",
                        text: res.message || "User baru berhasil dibuat dan ditugaskan ke brand ini.",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    $('#modal_add_user_alert').html(res.message || 'Gagal menyimpan user.').fadeIn();
                }
            },
            error: function() {
                $('#modal_add_user_alert').html('Terjadi kesalahan koneksi server.').fadeIn();
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // 2. Submit Assign Existing User Modal
    $('#form_assign_user').submit(function(e) {
        e.preventDefault();
        $('#modal_assign_user_alert').hide().html('');

        var formData = $(this).serializeArray();
        formData.push({ name: csrfName, value: csrfHash });

        var $btn = $('#btn_save_assign_user');
        var originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menugaskan...');

        $.ajax({
            url: BASE_URL + 'administrator/brands/assign_user',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(res) {
                if (res.success) {
                    $('#modal_assign_user').modal('hide');
                    swal({
                        title: "Berhasil!",
                        text: res.message || "User berhasil ditugaskan ke brand ini.",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    $('#modal_assign_user_alert').html(res.message || 'Gagal menugaskan user.').fadeIn();
                }
            },
            error: function() {
                $('#modal_assign_user_alert').html('Terjadi kesalahan koneksi server.').fadeIn();
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // 3. Unassign User Action
    $(document).on('click', '.btn-unassign-user', function() {
        var userId = $(this).data('user-id');
        var username = $(this).data('username');

        swal({
            title: "Lepas Pengguna dari Brand?",
            text: "Apakah Anda yakin ingin melepas user '" + username + "' dari brand ini? User akan berstatus unassigned.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Lepaskan",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function(isConfirm) {
            if (!isConfirm) return;

            var postData = {
                user_id: userId,
                brand_id: currentBrandId
            };
            postData[csrfName] = csrfHash;

            $.ajax({
                url: BASE_URL + 'administrator/brands/unassign_user',
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(res) {
                    if (res.success) {
                        swal({
                            title: "Berhasil!",
                            text: res.message || "User berhasil dilepas dari brand.",
                            type: "success",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        $('#row_member_' + userId).fadeOut(400, function(){
                            $(this).remove();
                            var remainingCount = $('#tbody_team_members tr[id^="row_member_"]').length;
                            $('#kpi_team_count').text(remainingCount);
                            if (remainingCount === 0) {
                                $('#tbody_team_members').html(
                                    '<tr id="row_empty_team"><td colspan="7" class="text-center text-muted" style="padding: 24px;"><i class="fa fa-info-circle fa-2x" style="display: block; margin-bottom: 6px; color: #cbd5e1;"></i>Belum ada anggota tim yang ditugaskan pada brand ini.</td></tr>'
                                );
                            }
                        });
                    } else {
                        swal("Gagal!", res.message || "Tidak dapat melepas user.", "error");
                    }
                },
                error: function() {
                    swal("Error!", "Terjadi kesalahan koneksi server.", "error");
                }
            });
        });
    });

    // 4. Quick Context Switcher Action
    $('.btn-switch-context').click(function() {
        var brandId = $(this).data('brand-id');
        var postData = { brand_id: brandId };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: BASE_URL + 'administrator/brands/switch_context',
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function(res) {
                if (res.success) {
                    swal({
                        title: "Konteks Aktif Berubah!",
                        text: "Sistem sekarang beroperasi dalam konteks brand ini.",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function(){ location.reload(); }, 1200);
                } else {
                    swal("Gagal!", res.message || "Tidak dapat beralih konteks.", "error");
                }
            },
            error: function() {
                swal("Error!", "Terjadi kesalahan saat beralih konteks brand.", "error");
            }
        });
    });
});
</script>
