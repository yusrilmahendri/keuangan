
<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
	<div class="profile-sidebar">
		<!-- <div class="profile-userpic">
			<img src="{{ asset('../images/default.jpg') }}" class="img-responsive" alt="#">
		</div> -->
		<div class="profile-usertitle">
			<div class="profile-usertitle-name" style="
                margin-top:10px;
                font-size: 22px;
                font-weight: 800;
                color: #333;
                text-shadow: 0 0 3px rgba(0,0,0,0.15);
                font-family: 'Montserrat', sans-serif;
            ">
                Welcome to our finances
            </div>
		</div>
		<div class="clear"></div>
	</div>

	<div class="divider"></div>
	<ul class="nav menu">

		<li class="active">
			<a href="{{ url('dashboard') }}">
				<em class="fa fa-dashboard">&nbsp;</em>
				Dashboard</a>
		</li>

		<li class="parent ">
			<a data-toggle="collapse" href="#sub-item-1">
				<em class="fa fa-money">&nbsp;</em>
				Keuangan
				<span data-toggle="collapse" href="#sub-item-1" class="icon pull-right">
					<em class="fa fa-plus"></em></span>
			</a>

			<ul class="children collapse" id="sub-item-1">
				<li>
					<a class="#" href="{{ url('saldos') }}">
						<span class="fa fa-arrow-right">&nbsp;</span>
						Saldo
					</a>
				</li>

				<li>
					<a class="" href="{{ url('budgets') }}">
						<span class="fa fa-arrow-right">&nbsp;</span>
						Budgets
					</a>
				</li>
			</ul>
		</li>

        <li class="#">
            <a href="{{ url('transactions') }}">
                <em class="fa fa-shopping-cart">&nbsp;</em>
                Transactions</a>
        </li>
	
	</ul>
</div>