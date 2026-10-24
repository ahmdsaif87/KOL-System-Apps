<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
// Binding hotkeys
function domo() {
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

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kol Shipments <small><?= cclang('detail', ['Kol Shipments']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/kol_shipments'); ?>">Kol Shipments</a></li>
        <li class="active"><?= cclang('detail'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
                <div class="box-body" style="padding: 20px;">
                    <!-- Widget Header -->
                    <div class="box box-widget widget-user-2" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid #e7edf3; margin-bottom: 20px;">
                            <div>
                                <h3 style="margin: 0; color: #172b4d; font-size: 20px; font-weight: 700;">Kol Shipments</h3>
                                <div style="margin-top: 5px; color: #6b778c; font-size: 13px;"><?= cclang('detail', ['Kol Shipments']); ?></div>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                            </div>
                        </div>
                    </div>

                    <div class="form-horizontal" name="form_kol_shipments" id="form_kol_shipments">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="margin-bottom: 0;">
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Id</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->id); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Campaign Kol Id</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->campaign_kols_kol_id); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Recipient Name</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->recipient_name); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Shipping Address</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->shipping_address); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Courier</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->courier); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Tracking No</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->tracking_no); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Shipping Fee</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->shipping_fee); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Status</td>
                                    <td style="vertical-align: middle;"><?= render_status_badge($kol_shipments->status); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Shipped At</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->shipped_at ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Delivered At</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->delivered_at ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Notes</td>
                                    <td style="vertical-align: middle;"><?= nl2br(_ent($kol_shipments->notes ?: '-')); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Created By</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->aauth_users_full_name); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Created At</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->created_at); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; font-weight: 600; color: #172b4d; vertical-align: middle; background: #f6f8fb;">Updated At</td>
                                    <td style="vertical-align: middle;"><?= _ent($kol_shipments->updated_at); ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Shipped Product Details -->
                        <div style="margin-top: 25px; margin-bottom: 15px;">
                            <h4 style="color: #172b4d; font-weight: 700; margin-bottom: 12px;"><i class="fa fa-cubes text-primary"></i> Daftar Barang yang Dikirim (Shipped Items)</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" style="border-radius: 6px; overflow: hidden;">
                                    <thead style="background: #f6f8fb; color: #52606d; font-size: 11px; text-transform: uppercase;">
                                        <tr>
                                            <th width="40" class="text-center">#</th>
                                            <th>SKU</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th class="text-right">Harga Satuan</th>
                                            <th class="text-center" width="80">Qty</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($shipment_items)): ?>
                                            <?php $no = 1; $total_qty = 0; $total_val = 0; ?>
                                            <?php foreach ($shipment_items as $item): ?>
                                                <?php 
                                                    $subtotal = (float)$item->product_price * (int)$item->qty;
                                                    $total_qty += (int)$item->qty;
                                                    $total_val += $subtotal;
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td><span class="label label-info"><?= _ent($item->product_sku ?: 'PROD'); ?></span></td>
                                                    <td style="font-weight: 600;"><?= _ent($item->product_name); ?></td>
                                                    <td><?= _ent($item->product_category ?: '-'); ?></td>
                                                    <td class="text-right">Rp <?= number_format((float)$item->product_price, 0, ',', '.'); ?></td>
                                                    <td class="text-center" style="font-weight: 700;"><?= (int)$item->qty; ?> pcs</td>
                                                    <td class="text-right" style="font-weight: 600; color: #00a65a;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr style="background: #f9fbfd; font-weight: 700;">
                                                <td colspan="5" class="text-right">Total Produk:</td>
                                                <td class="text-center"><?= $total_qty; ?> pcs</td>
                                                <td class="text-right" style="color: #00a65a;">Rp <?= number_format($total_val, 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted" style="padding: 15px;">Tidak ada rincian produk untuk pengiriman ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="view-nav" style="margin-top: 18px;">
                            <?php is_allowed('kol_shipments_update', function() use ($kol_shipments){ ?>
                                <a class="btn btn-flat btn-primary btn_edit btn_action" id="btn_edit" data-stype="back" title="edit kol_shipments (Ctrl+e)" href="<?= site_url('administrator/kol_shipments/edit/' . $kol_shipments->id); ?>">
                                    <i class="fa fa-pencil"></i> <?= cclang('update', ['Kol Shipments']); ?>
                                </a>
                            <?php }) ?>
                            <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/kol_shipments/'); ?>">
                                <i class="fa fa-undo"></i> <?= cclang('go_list_button', ['Kol Shipments']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->
