$(document).ready(function(){
	$(".product-group-nav").click(function(){
		var group_name = $(this).attr("data-group-name");
		var group_slug = $(this).attr("data-group-slug");
		var group_type = $(this).attr("data-product-type");
		$(".prod-slider-seeall").text("See all "+group_name+" Countries");
		$(".prod-slider-seeall").attr("href", "/group-page/?type="+group_type+"&group="+group_slug);
	});
	$('input[name="Trademark_inuse"]').change(function(){
		if($(this).val() == "no"){
			$(".tdate").hide();
		} else {
			$(".tdate").show();
		}
	});
	$("#type").change(function(){
		if($(this).val() != "wordmark"){
			$("#img-upload").show();
		} else {
			$("#img-upload").hide();
		}
	});
	$(".unsure").change(function(){
		var unsure_group = $(this).attr('name');
		if($(this).is(":checked")){
			$("#"+unsure_group+"-notes").show();
			$("#"+unsure_group+"-notes textarea").removeAttr("disabled");
		} else {
			$("#"+unsure_group+"-notes").hide();
			$("#"+unsure_group+"-notes textarea").attr("disabled","disabled");
		}
	})
	$('.addon-select').change(function(){
		if($(this).is(":checked")){
			cart_state.selected_addons.push($(this).val());
		} else {
			var index = cart_state.selected_addons.indexOf($(this).val());
			if(index > -1){
				cart_state.selected_addons.splice(index,1);
			}
		}
		render();
	});
	$('input[name="additional_services"]').change(function(){
		if($(this).is(":checked")){
			addToCartAdditionalService($(this).val(), $(this).data('price'));
		} else {
			removeFromCartAdditionalService($(this).val(), $(this).data('price'));
		}
	});
	/*$("#paymentForm").submit(function(e){
		e.preventDefault();
		var payment_method = $("input[name='paymentmethod']:checked").val();
		if(payment_method == "worldpay"){
			Worldpay.submitTemplateForm();
		} else {
			e.currentTarget.submit();
		}
	})*/
	$('input[name="product_images"]').change(function () {
		var file = $('input[name="product_images"]')[0].files[0].name;
  		$(".filename").text(file);
	});
	var qs = (function(a) {
	    if (a == "") return {};
	    var b = {};
	    for (var i = 0; i < a.length; ++i)
	    {
	        var p=a[i].split('=', 2);
	        if (p.length == 1)
	            b[p[0]] = "";
	        else
	            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
	    }
	    return b;
	})(window.location.search.substr(1).split('&'));

	if(typeof qs['orderid'] != "undefined" && qs['orderid'] != null){
		document.body.outerHTML = document.body.outerHTML.replace("{Order ID}", qs['orderid']);
	}

});

function render(){
	var productPrice = cart_state.product.product_price;
	var classPrice = cart_state.product.class_price;
	var addonCount = cart_state.selected_addons.length;
	var aditionalAddonCount = (addonCount-1) > 0 ? addonCount-1 : 0;
	var addonPrice = aditionalAddonCount*classPrice;
	cart_state.total_price = parseInt(productPrice)+parseInt(addonPrice);
	$('.amount').text('$'+cart_state.total_price);
	$('#addon-summary').text(aditionalAddonCount+' x $'+classPrice);
	$('#cart-total').text("$"+cart_state.total_price);
	$('#count').val(aditionalAddonCount+1);
}

function delPackage(id, packageId){
	$.ajax({
		url:"/wp-json/ecom/api/remove-from-cart",
		type:"POST",
		data:{id:id},
		dataType:"json",
		success: function(response){
			var price = parseInt($("#"+packageId).attr('data-price'));
			var grandTotal = parseInt($("#grandtotal").attr('data-grandtotal'));
			grandTotal = grandTotal-price;
			$("#grandtotal").text("$"+grandTotal);
			$("#grandtotal").attr('data-grandtotal', grandTotal);
			$("#"+packageId).remove();
		}

	});
}

function addToCartAdditionalService(id, price){
	$.ajax({
		url:"/wp-json/ecom/api/add-to-cart-additional-service",
		type:"POST",
		data:{id:id},
		dataType:"json",
		success: function(response){
			var grandTotal = parseInt($("#grandtotal").attr('data-grandtotal'));
			grandTotal = grandTotal + parseInt(price);
			$("#grandtotal").text("$"+grandTotal);
			$("#grandtotal").attr('data-grandtotal', grandTotal);
		}

	});
}

function removeFromCartAdditionalService(id, price){
	$.ajax({
		url:"/wp-json/ecom/api/remove-from-cart-additional-service",
		type:"POST",
		data:{id:id},
		dataType:"json",
		success: function(response){
			var grandTotal = parseInt($("#grandtotal").attr('data-grandtotal'));
			grandTotal = grandTotal - parseInt(price);
			$("#grandtotal").text("$"+grandTotal);
			$("#grandtotal").attr('data-grandtotal', grandTotal);
		}

	});
}

function scrolltoslider(){
    if(document.getElementById("carousel-example-generic") != null){
        document.getElementById("carousel-example-generic").scrollIntoView({behavior:"smooth"});   
    }
}

function previewImages(event) {
	var preview = document.getElementById('image-preview');
	preview.innerHTML = ''; // Clear previous previews
	var files = event.target.files;
	for (var i = 0; i < files.length; i++) {
		var file = files[i];

		if (!file.type.match('image.*')) {
			continue;
		}

		var reader = new FileReader();

		reader.onload = function (event) {
			var img = document.createElement('img');
			img.src = event.target.result;
			img.style.maxWidth = '100px'; // Adjust image size as needed
			preview.appendChild(img);
		}

		reader.readAsDataURL(file);
	}
}

