<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function domo(){
   $('*').bind('keydown', 'Ctrl+e', function assets() {
      $('#btn_edit').trigger('click');
       return false;
   });
   $('*').bind('keydown', 'Ctrl+x', function assets() {
      window.location.href = BASE_URL + '/administrator/user';
       return false;
   });
}
jQuery(document).ready(domo);
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('user'); ?>
      <small><?= cclang('detail', cclang('user')); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> <?= cclang('home'); ?></a></li>
      <li class=""><a  href="<?= site_url('administrator/user'); ?>"><?= cclang('user'); ?></a></li>
      <li class="active"><?= cclang('detail'); ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >
      <div class="col-md-12">
         <div class="box" style="border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(23,43,77,0.10);">
            <div class="box-body" style="padding: 20px;">
                  
                  <div class="row">
                     <div class="col-md-12">
                        <table class="table table-bordered table-striped" style="border: 1px solid #dfe6ee; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                           <tbody>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle; width: 250px;">Full Name</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent(ucwords($user->full_name)); ?></td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">Username</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent($user->username); ?></td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">Status</th>
                                 <td style="color: #333; vertical-align: middle;">
                                    <?php if ($user->banned) :?>
                                      <span class="badge bg-red"><?= cclang('banned'); ?></span>
                                    <?php else: ?>
                                      <span class="badge bg-blue"><?= cclang('active'); ?></span>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;"><?= cclang('group', cclang('user')); ?></th>
                                 <td style="color: #333; vertical-align: middle;">
                                    <?php foreach($this->aauth->get_user_groups($user->id) as $row): ?>
                                      <span class="label label-default" style="margin-right: 5px;"><?= $row->name; ?></span>
                                    <?php endforeach; ?>
                                 </td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">Last Login</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent($user->last_login); ?></td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">Last Activity</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent($user->last_activity); ?></td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">Date Created</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent($user->date_created); ?></td>
                              </tr>
                              <tr>
                                 <th style="background-color: #f6f8fb; color: #52606d; vertical-align: middle;">IP Address</th>
                                 <td style="color: #333; vertical-align: middle;"><?= _ent($user->ip_address); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>

                  <br>
                  <div class="view-nav">
                        <?php is_allowed('user_update', function() use ($user){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit user (Ctrl+e)" href="<?= site_url('administrator/user/edit/'.$user->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', cclang('user')); ?></a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/user/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list', cclang('user')); ?></a>
                  </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->

      </div>
   </div>
</section>
<!-- /.content -->
