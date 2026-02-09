<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xl-2 col-lg-4 col-12">
				  <button class="btn btn-danger w-p100 mb-30" type="button" alt="default" data-bs-toggle="modal" data-bs-target=".bs-example-modal-lg">Compose</button>

				  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="myLargeModalLabel">Compose New Message</h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<input class="form-control" placeholder="To:">
								  </div>
								  <div class="form-group">
									<input class="form-control" placeholder="Subject:">
								  </div>
								  <div class="form-group">
										<textarea id="compose-textarea" class="form-control" style="height: 300px">
										  <p>Your Message Here....</p>
										</textarea>
								  </div>
								  <div class="form-group">
									<div class="btn btn-info btn-file">
									  <i class="fa fa-paperclip"></i> Attachment
									  <input type="file" name="attachment">
									</div>
									<p class="help-block">Max. 32MB</p>
								  </div>
							</div>
							<div class="modal-footer">
								<div class="pull-right">
									<button type="button" class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
									<button type="submit" class="btn btn-success"><i class="fa fa-envelope-o"></i> Send</button>
								</div>
								<button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> Discard</button>
								<button type="button" class="btn btn-danger text-start" data-bs-dismiss="modal">Close</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				  <div class="box">
					<div class="box-header with-border">
					  <h4 class="box-title">Folders</h4>
					  <ul class="box-controls pull-right">
						<li><a class="box-btn-slide" href="#"></a></li>	
					  </ul>
					</div>
					<div class="box-body no-padding mailbox-nav">
					  <ul class="nav nav-pills flex-column">
						<li class="nav-item"><a class="nav-link active" href="javascript:void(0)"><i class="ion ion-ios-email-outline"></i> Inbox
						  <span class="label label-success pull-right">12</span></a></li>
						<li class="nav-item"><a class="nav-link" href="javascript:void(0)"><i class="ion ion-paper-airplane"></i> Sent</a></li>
						<li class="nav-item"><a class="nav-link" href="javascript:void(0)"><i class="ion ion-email-unread"></i> Drafts</a></li>
						<li class="nav-item"><a class="nav-link" href="javascript:void(0)"><i class="ion ion-star"></i>  Starred <span class="label label-warning pull-right">14</span></a>
						</li>
						<li class="nav-item"><a class="nav-link" href="javascript:void(0)"><i class="ion ion-trash-a"></i> Trash</a></li>
					  </ul>
					</div>
					<!-- /.box-body -->
				  </div>
				  <!-- /. box -->
				  <div class="box">
					<div class="box-header with-border">
					  <h4 class="box-title">Labels</h4>
					  <ul class="box-controls pull-right">
						<li><a class="box-btn-slide" href="#"></a></li>	
					  </ul>
					</div>
					<div class="box-body no-padding mailbox-nav">
					  <ul class="nav nav-pills flex-column">
						<li class="nav-item"><a class="nav-link" href="#"><i class="fa fa-circle-o text-danger"></i> Important</a></li>
						<li class="nav-item"><a class="nav-link" href="#"><i class="fa fa-circle-o text-warning"></i> Promotions</a></li>
						<li class="nav-item"><a class="nav-link" href="#"><i class="fa fa-circle-o text-info"></i> Social</a></li>
					  </ul>
					</div>
					<!-- /.box-body -->
				  </div>
				  <!-- /.box -->
					
					<div class="contact-bx">
						<div class="media-list media-list-hover">
							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-success" href="#">
								<img src="../images/avatar/1.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Sarah Kortney</a>
								</p>
							  </div>
							</div>

							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-danger" href="#">
								<img src="../images/avatar/2.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Tommy Nash</a>
								</p>
							  </div>
							</div>

							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-warning" href="#">
								<img src="../images/avatar/3.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Kathryn Mengel</a>
								</p>
							  </div>
							</div>

							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-primary" href="#">
								<img src="../images/avatar/4.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Mayra Sibley</a>
								</p>
							  </div>
							</div>			

							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-success" href="#">
								<img src="../images/avatar/1.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Tommy Nash</a>
								</p>
							  </div>
							</div>

							<div class="media py-10 px-0 align-items-center">
							  <a class="avatar avatar-lg status-danger" href="#">
								<img src="../images/avatar/2.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p class="fs-16">
								  <a href="#">Williemae Lagasse</a>
								</p>
							  </div>
							</div>
						  </div>
					</div>
				</div>
				<!-- /.col -->
				<div class="col-xl-6 col-lg-8 col-12">
				  <div class="box">
					<div class="box-header with-border">
					  <h4 class="box-title">Inbox</h4>
						<div class="box-controls pull-right">
						<div class="box-header-actions">
						  <div class="lookup lookup-sm lookup-right d-none d-lg-block">
							<input type="text" name="s" placeholder="Search">
						  </div>
						</div>
					  </div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
					  <div class="mailbox-controls">
						<!-- Check all button -->
						<button type="button" class="btn btn-primary btn-sm checkbox-toggle"><i class="ion ion-android-checkbox-outline-blank"></i>
						</button>
						<div class="btn-group">
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-trash-a"></i></button>
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-reply"></i></button>
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-share"></i></button>
						</div>
						<!-- /.btn-group -->
						<div class="btn-group">
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							  <i class="ion ion-flag margin-r-5"></i>
							  <span class="caret"></span>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="#">Action</a>
								<a class="dropdown-item" href="#">Another action</a>
								<a class="dropdown-item" href="#">Something else here</a>
							</div>
						  </div>
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							  <i class="ion ion-folder margin-r-5"></i>
							  <span class="caret"></span>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="#">Action</a>
								<a class="dropdown-item" href="#">Another action</a>
								<a class="dropdown-item" href="#">Something else here</a>
							</div>
						  </div>
						</div>
						<!-- /.btn-group -->
						<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button>
						<div class="pull-right">
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i></button>
							<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-chevron-right"></i></button>
						  </div>
						  <!-- /.btn-group -->
						</div>
						<!-- /.pull-right -->
					  </div>
					  <div class="mailbox-messages inbox-bx">
						  <div class="table-responsive">
							<table class="table table-hover table-striped">
							  <tbody>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>
								<td class="mailbox-date">2:45 PM</td>

							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							 <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"><i class="fa fa-paperclip"></i></td>

								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  <tr>
								<td><input type="checkbox"></td>
								<td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
								<td>
									<p class="mailbox-name mb-0 fs-16 fw-600">Johen Doe</p>
									<a class="mailbox-subject" href="#"><b>Simple text</b> - There are many variations of Ipsum available...</a>
								</td>
								<td class="mailbox-attachment"></td>
								<td class="mailbox-date">2:45 PM</td>
							  </tr>
							  </tbody>
							</table>
						  </div>                
						<!-- /.table -->
					  </div>
					  <!-- /.mail-box-messages -->
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
					  <div class="mailbox-controls">
						<!-- Check all button -->
						<button type="button" class="btn btn-primary btn-sm checkbox-toggle"><i class="ion ion-android-checkbox-outline-blank"></i>
						</button>
						<div class="btn-group">
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-trash-a"></i></button>
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-reply"></i></button>
						  <button type="button" class="btn btn-primary btn-sm"><i class="ion ion-share"></i></button>
						</div>
						<!-- /.btn-group -->
						<div class="btn-group">
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							  <i class="ion ion-flag margin-r-5"></i>
							  <span class="caret"></span>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="#">Action</a>
								<a class="dropdown-item" href="#">Another action</a>
								<a class="dropdown-item" href="#">Something else here</a>
							</div>
						  </div>
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							  <i class="ion ion-folder margin-r-5"></i>
							  <span class="caret"></span>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="#">Action</a>
								<a class="dropdown-item" href="#">Another action</a>
								<a class="dropdown-item" href="#">Something else here</a>
							</div>
						  </div>
						</div>
						<!-- /.btn-group -->
						<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button>
						<div class="pull-right">
						  <div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i></button>
							<button type="button" class="btn btn-primary btn-sm"><i class="fa fa-chevron-right"></i></button>
						  </div>
						  <!-- /.btn-group -->
						</div>
						<!-- /.pull-right -->
					  </div>
					</div>
				  </div>
				  <!-- /. box -->
				</div>
				<!-- /.col -->
				<div class="col-xl-4 col-12">
				  <div class="box">
					<div class="box-body pt-10">
					  <div class="mailbox-read-info">
						<h4>Your message title goes here</h4>
					  </div>
					  <div class="mailbox-read-info clearfix mb-20">
						<div class="float-start me-10"><a href="#"><img src="../images/1.jpg" alt="user" width="40" class="rounded-circle"></a></div>
						<h5 class="no-margin"> Pavan kumar<br>
							 <small>From: jonathan@domain.com</small>
						  <span class="mailbox-read-time pull-right">22 JUL. 2023 08:03 PM</span></h5>
					  </div>
					  <!-- /.mailbox-read-info -->
					  <div class="mailbox-controls with-border clearfix">                
						<div class="float-start">
						  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Print">
						  <i class="fa fa-print"></i></button>
						</div>
						<div class="float-end">
						<div class="btn-group">
						  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-container="body" title="Delete">
							<i class="fa fa-trash-o"></i></button>
						  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-container="body" title="Reply">
							<i class="fa fa-reply"></i></button>
						  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-container="body" title="Forward">
							<i class="fa fa-share"></i></button>
						</div>
						</div>
						<!-- /.btn-group -->

					  </div>
					  <!-- /.mailbox-controls -->
					  <div class="mailbox-read-message read-mail-bx">
						<p>Dear USer,</p>

						<p>Simple text dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi.</p>

						<p>enean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar.</p>

						<p>Thanks,<br>Jane</p>
					  </div>
					  <!-- /.mailbox-read-message -->
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<h5><i class="fa fa-paperclip m-r-10 m-b-10"></i> Attachments <span>(3)</span></h5>
					  <ul class="mailbox-attachments clearfix">
						<li>
						  <div class="mailbox-attachment-info">
							<a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> Mag.pdf</a>
								<span class="mailbox-attachment-size">
								  5,215 KB
								  <a href="#" class="btn btn-primary btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
								</span>
						  </div>
						</li>
						<li>
						  <div class="mailbox-attachment-info">
							<a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> Documents.docx</a>
								<span class="mailbox-attachment-size">
								  2,145 KB
								  <a href="#" class="btn btn-primary btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
								</span>
						  </div>
						</li>
					  </ul>
					</div>
					<!-- /.box-footer -->
					<div class="box-footer">
					  <div class="pull-right">
						<button type="button" class="btn btn-success"><i class="fa fa-reply"></i> Reply</button>
						<button type="button" class="btn btn-info"><i class="fa fa-share"></i> Forward</button>
					  </div>
					  <button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
					  <button type="button" class="btn btn-warning"><i class="fa fa-print"></i></button>
					</div>
					<!-- /.box-footer -->
				  </div>
				  <!-- /. box -->
				</div>
				<!-- /.col -->
			  </div>
		</section>
		<!-- /.content -->
	  </div>
  </div>
  
<!-- /.content-wrapper -->
<?php
$hide_dashboard_js = true;
$extra_js = "<script src='../assets/vendor_plugins/iCheck/icheck.js'></script>
<script src='../assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'></script>
<script src='js/pages/mailbox.js'></script>
<script src='js/pages/form-compose.js'></script>";
include 'includes/footer.php';
?>
