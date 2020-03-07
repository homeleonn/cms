<style>


table.admin{
	min-width: 80%;
	border-collapse: collapse;
	margin: 0 auto;
}

table.admin td{
	padding: 5px;
}



#file-response{
	
}

#file-response #loading{
	margin-bottom: 10px;
	width: 100px; 
	position:relative; 
	border: 1px silver solid; 
	height: 12px;
	display: none;
}

#file-response #loading img{
	height:10px; 
	position: absolute; 
	top:0px;
}

#file-response #loading-proress{
	margin: 10px 0;
}

#file-response > input{
	margin: 5px 0;
	display: none;
}

.catList{
	width:180px;
}

.catList option{
	padding: 3px;
	cursor: pointer;
}

/*events*/
.events{
	margin: 10px 0;
	padding: 10px;
}

.events.err{
	background: indianred;
}

.events.success{
	background: lightgreen;
}





/*add-item*/

#add-item{
	height: 100%; 
	text-align: center;
	display: block;
	border: 2px lightgreen solid;
	box-shadow: 3px 3px 10px rgba(144, 238, 144, 1);
}

#add-item img{
	width: 100px; 
	position: relative; 
	top: 50%; 
	margin-top: -70px;
}



.imgIndex .del{
	position: absolute;
	top: 5px;
	right: 5px;
	cursor: pointer;
	opacity: .7;
}


/*****/

#add-item1{
	width: 200px;
	height: 500px;
}


#table{
	width: 100%;
}

#table input.w100{
	width: 100%;
}


#table input[type="text"]{
	padding: 8px;
}


#table tr td:first-child{
	max-width: 100px;
}


/*pricelist*/
#prcltools{
	/*position: absolute;
	right: 20px;
	top: -20px;
	background: white;*/
	margin: 20px;
}

#prcltools span{
	margin-left: 10px;
}



/*TOOLS*/
.tools, .tools1{
	background: white;
	padding: 5px;
	position: absolute; 
	right: 20px; 
	top: 16px;
	height: 32px;
}

.tools *,
.tools1 *{
	max-height: 100%;
}

.cat > .tools{
	height: auto;
}

.tools1{
	right: 0px; 
	top: 0px;
}

.tools a,
.tools1 a{
	width: 20px;
	position: relative;
	display: inline-block;
}

.tools a:first-child,
.tools1 a:first-child{
	margin-right: 5px;
}

.tools a:hover,
.tools1 a:hover{
	top: 2px;
}

.tools img,
.tools img{
	width: 100%;
}

#adminpanel{
	position: fixed;
	width: 100px;
	top: 0px;
	right: 0px;
	background: #dedede;
	z-index: 999;
}

#adminpanel a{
	color: white !important;
	display: block;
	text-decoration: none;
	padding: 5px;
	border-bottom: 1px white solid;
}


#adminpanel a:hover{
	color: #00d5ff !important;
	background: #212121;
}

#adminpanel > div{
	color:#008a00;
	padding: 5px;
	text-align: center;
	cursor: pointer;
}

#adminpanel > ul{
	position: absolute;
	width: 230px;
	background: #292929;
	right: -230px;
	transition: .2s;
}

#adminpanel li{
	display: block;
}

#adminpanel > ul.showadmin{
	right: 0px;
}

body{
	padding-top: 30px;
}

</style>