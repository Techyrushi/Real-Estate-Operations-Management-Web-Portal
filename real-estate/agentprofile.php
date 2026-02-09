<?php include 'includes/header.php'; ?>
  
<?php include 'includes/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-12 col-lg-5 col-xl-4">
				  <div class="box">
						<div class="box-body">
							<div class="d-flex flex-row">
								<div class=""><img src="../images/avatar/1.jpg" alt="user" class="rounded-circle" width="100"></div>
								<div class="ps-20">
									<h3><?php echo $_SESSION['username'] ?? 'User'; ?></h3>
									<h6><?php echo $_SESSION['email'] ?? 'user@example.com'; ?></h6>
									<button class="btn btn-success"><i class="ti-plus"></i> Contact</button>
								</div>
							</div>
							<div class="row mt-40">
								<div class="col text-center">
									<h2 class="font-light">14</h2>
									<h6>Files</h6>
								</div>
								<div class="col text-center">
									<h2 class="font-light">12GB</h2>
									<h6>Used</h6>
								</div>
								<div class="col text-center">
									<h2 class="font-light">₹25k</h2>
									<h6>Spent</h6>
								</div>
							</div>
						</div>
						<div class="box-body">
							<p class="text-center">
								Simple text dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae.
							</p>
							<ul class="list-inline text-center">
								<li><a href="javascript:void(0)"><i class="fa fa-instagram fs-20"></i></a></li>
								<li><a href="javascript:void(0)"><i class="fa-brands fa-x-twitter fs-20"></i></a></li>
								<li><a href="javascript:void(0)"><i class="fa fa-facebook-square fs-20"></i></a></li>
							</ul>
						</div>
					</div>		
				  <div class="box">
					<div class="box-body box-profile">            
					  <div class="row">
						<div class="col-12">
							<div>
								<p>Email :<span class="text-gray ps-10">David@yahoo.com</span> </p>
								<p>Phone :<span class="text-gray ps-10">+11 123 456 7890</span></p>
								<p>Address :<span class="text-gray ps-10">123, Simple text, Florida, USA</span></p>
							</div>
						</div>
						<div class="col-12">
							<div class="pb-15">						
								<p class="mb-10">Social Profile</p>
								<div class="user-social-acount">
									<button class="btn btn-circle btn-social-icon btn-facebook"><i class="fa fa-facebook"></i></button>
									<button class="btn btn-circle btn-social-icon btn-twitter"><i class="fa-brands fa-x-twitter"></i></button>
									<button class="btn btn-circle btn-social-icon btn-instagram"><i class="fa fa-instagram"></i></button>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div>
								<div class="map-box">
									<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2805244.1745767146!2d-86.32675167439648!3d29.383165774894163!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88c1766591562abf%3A0xf72e13d35bc74ed0!2sFlorida%2C+USA!5e0!3m2!1sen!2sin!4v1501665415329" width="100%" height="100" frameborder="0" style="border:0" allowfullscreen></iframe>
								</div>
							</div>
						</div>
					  </div>
					</div>
					<!-- /.box-body -->
				  </div>				  
				  <div class="box">
					<div class="box-body">
						<div class="flexbox align-items-baseline mb-20">
						  <h6 class="text-uppercase">Team</h6>
						  <small>20</small>
						</div>
						<div class="gap-items-2 gap-y">
						  <a class="avatar" href="#"><img src="../images/avatar/1.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/3.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/4.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/5.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/6.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/7.jpg" alt="..."></a>
						  <a class="avatar" href="#"><img src="../images/avatar/8.jpg" alt="..."></a>
						  <a class="avatar avatar-more" href="#">+15</a>
						</div>
					</div>
					<div class="box-footer">
						<a class="text-uppercase d-blockls-1 text-fade" href="#">Invite People</a>
					</div>
				</div>
				  <div class="box box-inverse box-carousel carousel slide" data-bs-ride="carousel" style="background-color: #00aced">
				  <div class="box-header no-border">
					<span class="fa-brands fa-x-twitter fs-30"></span>
					  <div class="box-tools pull-right">
						<h5 class="box-title">Carousel feed</h5>
					  </div>
				  </div>

				  <div class="carousel-inner">
					  <div class="carousel-item active">
					  	<blockquote class="blockquote blockquote-inverse no-border m-0 py-15">
						  <p>Holisticly benchmark plug imperatives for multifunctional deliverables. Seamlessly incubate cross functional action.</p>
						  <div class="flexbox">
							<time class="text-white" datetime="2017-11-22 20:00">22 November, 2017</time>
							<span><i class="fa fa-heart"></i> 62</span>
						  </div>
						</blockquote>
					  </div>
					  <div class="carousel-item">
					  	<blockquote class="blockquote blockquote-inverse no-border m-0 py-15">
						  <p>Uniquely revolutionize leveraged catalysts for change for world-class web services. Efficiently underwhelm competitive.</p>
						  <div class="flexbox">
							<time class="text-white" datetime="2017-11-22 20:00">22 November, 2017</time>
							<span><i class="fa fa-heart"></i> 45</span>
						  </div>
						</blockquote>
					  </div>
					  <div class="carousel-item">
					  	<blockquote class="blockquote blockquote-inverse no-border m-0 py-15">
						  <p>Enthusiastically optimize cross-media manufactured products without process-centric web services. Conveniently.</p>
						  <div class="flexbox">
							<time class="text-white" datetime="2017-11-22 20:00">22 November, 2017</time>
							<span><i class="fa fa-heart"></i> 65</span>
						  </div>
						</blockquote>
					  </div>
				  </div>

				</div>
				<div class="box box-inverse" style="background-color: #3b5998">
				  <div class="box-header no-border">
					<span class="fa fa-facebook fs-30"></span>
					  <div class="box-tools pull-right">
						<h5 class="box-title">Facebook feed</h5>
					  </div>
				  </div>

				  <blockquote class="blockquote blockquote-inverse no-border m-0 py-15">
					<p>Holisticly benchmark plug imperatives for multifunctional deliverables. Seamlessly incubate cross functional action.</p>
					<div class="flexbox">
					  <time class="text-white" datetime="2017-11-21 20:00">21 November, 2017</time>
					  <span><i class="fa fa-heart"></i> 75</span>
					</div>
				  </blockquote>
				</div>
			  </div>
				<div class="col-12 col-lg-7 col-xl-8">
				
			  <div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
				  <li><a class="active" href="#usertimeline" data-bs-toggle="tab">Timeline</a></li>
				  <li><a href="#activity" data-bs-toggle="tab">Activity</a></li>
				  <li><a href="#settings" data-bs-toggle="tab">Settings</a></li>
				</ul>

				<div class="tab-content">

				 <div class="active tab-pane" id="usertimeline">
					<div class="publisher publisher-multi b-1 mb-30">
					  <textarea class="publisher-input auto-expand" rows="4" placeholder="Write something"></textarea>
					  <div class="flexbox">
						<div class="gap-items">
						  <span class="publisher-btn file-group">
							<i class="fa fa-image file-browser"></i>
							<input type="file">
						  </span>
						  <a class="publisher-btn" href="#"><i class="fa fa-map-marker"></i></a>
						  <a class="publisher-btn" href="#"><i class="fa fa-smile-o"></i></a>
						</div>

						<button class="btn btn-sm btn-bold btn-primary">Post</button>
					  </div>
					</div> 

					<div class="box">
					  <div class="media bb-1 border-fade">
						<img class="avatar avatar-lg" src="../images/avatar/3.jpg" alt="...">
						<div class="media-body">
						  <p>
							<strong>Denial Webar</strong>
							<time class="float-end text-fade" datetime="2017">24 min ago</time>
						  </p>
						  <p><small>Designer</small></p>
						</div>
					  </div>

					  <div class="box-body bb-1 border-fade">
						<p class="lead">Authoritatively syndicate goal-oriented leadership skills for clicks-and-mortar outsourcing. Synergistically reconceptualize enabled catalysts for change.</p>

						<div class="gap-items-4 mt-10">
						  <a class="text-fade hover-light" href="#">
							<i class="fa fa-thumbs-up me-1"></i> 1254
						  </a>
						  <a class="text-fade hover-light" href="#">
							<i class="fa fa-comment me-1"></i> 25
						  </a>
						  <a class="text-fade hover-light" href="#">
							<i class="fa fa-share-alt me-1"></i> 12
						  </a>
						</div>
					  </div>

					  <div class="media-list media-list-divided bg-lighter">
						<div class="media">
						  <a class="avatar" href="#">
							<img src="../images/avatar/6.jpg" alt="...">
						  </a>
						  <div class="media-body">
							<p>
							  <a href="#"><strong>Rock Tele</strong></a>
							  <time class="float-end text-fade" datetime="2017-07-14 20:00">Just now</time>
							</p>
							<p>Uniquely enhance world-class channels with just in time schemas.</p>

							<div class="media px-0 mt-20">
							  <a class="avatar" href="#">
								<img src="../images/avatar/8.jpg" alt="...">
							  </a>
							  <div class="media-body">
								<p>
								  <a href="#"><strong>Brock Lensar</strong></a>
								  <time class="float-end text-fade" datetime="2017-07-14 20:00">26 mins ago</time>
								</p>
								<p>Thank you for your nice comment.</p>
							  </div>
							</div>

						  </div>
						</div>

						<div class="media">
						  <a class="avatar" href="#">
							<img src="../images/avatar/9.jpg" alt="...">
						  </a>
						  <div class="media-body">
							<p>
							  <a href="#"><strong>Tony Stark</strong></a>
							  <time class="float-end text-fade" datetime="2017-07-14 20:00">2 hours ago</time>
							</p>
							<p>Continually drive user friendly solutions through performance based infomediaries.</p>
						  </div>
						</div>
					  </div>

					  <form class="publisher bt-1 border-fade">
						<img class="avatar avatar-sm" src="../images/avatar/4.jpg" alt="...">
						<input class="publisher-input" type="text" placeholder="Add Your Comment">
						<a class="publisher-btn" href="#"><i class="fa fa-smile-o"></i></a>
						<span class="publisher-btn file-group">
						  <i class="fa fa-camera file-browser"></i>
						  <input type="file">
						</span>
					  </form>

					</div>

					<div class="box p-15"> 
						<div class="timeline timeline-single-column timeline-single-full-column">
							
							<span class="timeline-label">
								<span class="badge badge-info badge-pill">Images</span>
							</span>

							<div class="timeline-item">
								<div class="timeline-point timeline-point-success">
									<i class="fa fa-image"></i>
								</div>
								<div class="timeline-event">
									<div class="timeline-heading">
										<h4 class="timeline-title"><a href="#">Rakesh Kumar</a><small> uploaded new photos</small></h4>
									</div>
									<div class="timeline-body">
										<img src="../images/150x100.png" alt="..." class="m-10">
										<img src="../images/150x100.png" alt="..." class="m-10">
										<img src="../images/150x100.png" alt="..." class="m-10">
										<img src="../images/150x100.png" alt="..." class="m-10">
									</div>
									<div class="timeline-footer">
										<p class="text-end"><i class="fa fa-clock-o"></i> 8 days ago</p>
									</div>
								</div>
							</div>

							<div class="timeline-item">
								<div class="timeline-point timeline-point-info">
									<i class="ion ion-chatbubble-working"></i>
								</div>
								<div class="timeline-event">
									<div class="timeline-heading">
										<h4 class="timeline-title"><a href="#">Jone Doe</a><small> commented on your post</small></h4>
									</div>
									<div class="timeline-body">
										<p>Simple text dolor sit amet, consectetur adipisicing elit. Repellendus numquam facilis enim eaque, tenetur nam id qui vel velit similique nihil iure molestias aliquam, voluptatem totam quaerat, magni commodi quisquam.</p>									
									</div>

									<div class="timeline-footer">
										<a class="btn btn-success btn-sm" href="#">View comment</a>
										<p class="pull-right"><i class="fa fa-clock-o"></i> 8 days ago</p>
									</div>
								</div>
							</div>

							<div class="timeline-item">
								<div class="timeline-point timeline-point-danger">
									<i class="ion ion-ios-videocam"></i>
								</div>
								<div class="timeline-event">
									<div class="timeline-heading">
										<h4 class="timeline-title"><a href="#">Jone Doe</a><small> shared a video</small></h4>
									</div>
									<div class="timeline-body">
										<div class="ratio ratio-16x9">
											<iframe src="https://www.youtube.com/embed/k85mRPqvMbE" frameborder="0" allowfullscreen></iframe>
										</div>									
									</div>
									<div class="timeline-footer">
										<a class="btn btn-success btn-sm" href="#">View comment</a>
										<p class="pull-right"><i class="fa fa-clock-o"></i> 8 days ago</p>
									</div>
								</div>
							</div>

							<span class="timeline-label">
								<button class="btn btn-danger"><i class="fa fa-clock-o"></i></button>
							</span>
						</div>
					</div>  
				  </div>    
				  <!-- /.tab-pane -->

				  <div class="tab-pane" id="activity">			

					<div class="box p-15">				
					<!-- Post -->
					<div class="post">
					  <div class="user-block">
						<img class="img-bordered-sm rounded-circle" src="../images/user1-128x128.jpg" alt="user image">
							<span class="username">
							  <a href="#">Brayden</a>
							  <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
							</span>
						<span class="description">5 minutes ago</span>
					  </div>
					  <!-- /.user-block -->
					  <div class="activitytimeline">
						  <p>
							Simple text dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum.
						  </p>
						  <ul class="list-inline">
							<li><a href="#" class="link-black text-sm"><i class="fa fa-share me-5"></i> Share</a></li>
							<li><a href="#" class="link-black text-sm"><i class="fa fa-thumbs-o-up me-5"></i> Like</a>
							</li>
							<li class="pull-right">
							  <a href="#" class="link-black text-sm"><i class="fa fa-comments-o me-5"></i> Comments
								(5)</a></li>
						  </ul>
						  <form class="form-element">
							  <input class="form-control input-sm" type="text" placeholder="Type a comment">
						 </form>
					  </div>
					</div>
					<!-- /.post -->

					<!-- Post -->
					<div class="post">
					  <div class="user-block">
						<img class="img-bordered-sm rounded-circle" src="../images/user6-128x128.jpg" alt="user image">
							<span class="username">
							  <a href="#">Evan</a>
							  <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
							</span>
						<span class="description">5 minutes ago</span>
					  </div>
					  <!-- /.user-block -->
					  <div class="activitytimeline">
						  <div class="row mb-20">
							<div class="col-sm-6">
							  <img class="img-fluid" src="../images/photo1.png" alt="Photo">
							</div>
							<!-- /.col -->
							<div class="col-sm-6">
							  <div class="row">
								<div class="col-sm-6">
								  <img class="img-fluid" src="../images/photo2.png" alt="Photo">
								  <br><br>
								  <img class="img-fluid" src="../images/photo3.jpg" alt="Photo">
								</div>
								<!-- /.col -->
								<div class="col-sm-6">
								  <img class="img-fluid" src="../images/photo4.jpg" alt="Photo">
								  <br><br>
								  <img class="img-fluid" src="../images/photo1.png" alt="Photo">
								</div>
								<!-- /.col -->
							  </div>
							  <!-- /.row -->
							</div>
							<!-- /.col -->
						  </div>
						  <!-- /.row -->

						  <ul class="list-inline">
							<li><a href="#" class="link-black text-sm"><i class="fa fa-share me-5"></i> Share</a></li>
							<li><a href="#" class="link-black text-sm"><i class="fa fa-thumbs-o-up me-5"></i> Like</a>
							</li>
							<li class="pull-right">
							  <a href="#" class="link-black text-sm"><i class="fa fa-comments-o me-5"></i> Comments
								(5)</a></li>
						  </ul>

						  <form class="form-element">
							  <input class="form-control input-sm" type="text" placeholder="Type a comment">
						 </form>
						</div>
					</div>
					<!-- /.post -->

					<!-- Post -->
					<div class="post clearfix">
					  <div class="user-block">
						<img class="img-bordered-sm rounded-circle" src="../images/user7-128x128.jpg" alt="user image">
							<span class="username">
							  <a href="#">Nicholas</a>
							  <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
							</span>
						<span class="description">5 minutes ago</span>
					  </div>
					  <!-- /.user-block -->
						<div class="activitytimeline">
						  <p>
							Simple text dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum.
						  </p>

						  <form class="form-horizontal form-element">
							<div class="form-group row no-gutters">
							  <div class="col-sm-9">
								<input class="form-control input-sm" placeholder="Response">
							  </div>
							  <div class="col-sm-3">
								<button type="submit" class="btn btn-danger pull-right w-p100 d-block btn-sm">Send</button>
							  </div>
							</div>
						  </form>
						</div>
					</div>
					<!-- /.post -->
				   </div>

				  </div>
				  <!-- /.tab-pane -->

				  <div class="tab-pane" id="settings">		

					<div class="box p-15">		
						<form class="form-horizontal form-element col-12">
						  <div class="form-group row">
							<label for="inputName" class="col-sm-2 control-label">Name</label>

							<div class="col-sm-10">
							  <input type="email" class="form-control" id="inputName" placeholder="">
							</div>
						  </div>
						  <div class="form-group row">
							<label for="inputEmail" class="col-sm-2 control-label">Email</label>

							<div class="col-sm-10">
							  <input type="email" class="form-control" id="inputEmail" placeholder="">
							</div>
						  </div>
						  <div class="form-group row">
							<label for="inputPhone" class="col-sm-2 control-label">Phone</label>

							<div class="col-sm-10">
							  <input type="tel" class="form-control" id="inputPhone" placeholder="">
							</div>
						  </div>
						  <div class="form-group row">
							<label for="inputExperience" class="col-sm-2 control-label">Experience</label>

							<div class="col-sm-10">
							  <textarea class="form-control" id="inputExperience" placeholder=""></textarea>
							</div>
						  </div>
						  <div class="form-group row">
							<label for="inputSkills" class="col-sm-2 control-label">Skills</label>

							<div class="col-sm-10">
							  <input type="text" class="form-control" id="inputSkills" placeholder="">
							</div>
						  </div>
						  <div class="form-group row">
							<div class="ml-auto col-sm-10">
							  <div class="checkbox">
								<input type="checkbox" id="basic_checkbox_1" checked="">
								<label for="basic_checkbox_1"> I agree to the</label>
								  &nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Terms and Conditions</a>
							  </div>
							</div>
						  </div>
						  <div class="form-group row">
							<div class="ml-auto col-sm-10">
							  <button type="submit" class="btn btn-success">Submit</button>
							</div>
						  </div>
						</form>
					</div>			  
				  </div>
				  <!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			  </div>
			  <!-- /.nav-tabs-custom -->
			</div>
		  </div>
		</section>
		<!-- /.content -->
	  </div>
  </div>
  <!-- /.content-wrapper -->
<?php
$hide_dashboard_js = true;
include 'includes/footer.php';
?>


