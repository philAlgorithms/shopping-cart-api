$(document).ready(function(){
      // Hide the loader and show the elements.
    setTimeout(function () {
      $('.loader').addClass('hidden').delay(200).remove();
      $('.slide-in').each(function() {
        $(this).addClass('visible');
      });
    }, 100);
    $('#alert').click(function(){
	hideAlert();
    });
});

function pushDownNavbar(){
  return $("#navbar").addClass("mt-5");
}

function pushUpNavbar(){
  return $("#navbar").removeClass("mt-5");
}

function disableInput(property){
  return property.prop("disabled",true);
}
function enableInput(property){
  return property.prop("disabled",false);
}

function disableV(property){
  return property.disabled = true;
}
function enableV(property){
  return property.disabled = false;
}

function disableB(property){
  return property.addClass('disabled');
}
function enableB(property){
  return property.removeClass('disabled');
}

function isEmpty(property){
  return property.val() === '';
}

function invalid(property, text){
  return property.addClass("is-invalid");
}
function valid(property){
  return property.removeClass("is-invalid");
}

function invalidate(property, text){
  property.siblings(".invalid-feedback").text(text);
  return property.addClass("is-invalid");
}

function validate(property){
	 property.siblings(".invalid-feedback").text("");
  return property.removeClass("is-invalid");
}

function capFirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function checkEmpty(property){
  if(property.val() === ''){
    invalidate(property, 'This field is required');
    return showAlert('danger',property.attr('placeholder')+' is required');
  }else{

    valid(property);
	  return hideAlert();
  }
}

function checkInput(inputData, toggleElement){
    if(Object.values(inputData).includes("")){
	return disableInput(toggleElement);
    }else{
        return enableInput(toggleElement);
    }
}

function aJ(object, mute){
    mute = (typeof mute === 'undefined') ? false : mute;
    if(mute === true){
	return;
    }else{
	return alert(JSON.stringify(object));
    }
}

function a(string, mute){
    mute = (typeof mute === 'undefined') ? false : mute;
    if(mute===true){
	return; 
    }else{
    	return alert(string);
    }
}

function checkPeriod(start,end, toggleElement,alertText){
  if(Math.sign(new Date(end).getTime() - new Date(start).getTime())<0){
    showAlert('danger', alertText);
    return "";
  }else{
    hideAlert();
    return "1";
  }
}

function localTime(datetime){
    var ms = new Date(datetime).getTime();
    var tzoffset = (new Date(datetime)).getTimezoneOffset() * 60000; //offset in milliseconds
    var localISOTime = (new Date(ms - tzoffset)).toISOString().slice(0, -1);
    
    return localISOTime;
}

function dateFormat(datetime){
    var date = new Date(datetime);
    var iso = date.toISOString().match(/(\d{4}\-\d{2}\-\d{2})T(\d{2}:\d{2}:\d{2})/);
    return iso[1] + ' ' + iso[2];
}

//Focus invalid input function coming up

function checkPeriodInput(start, end, checkObject, checkData, toggleElement,alertText){
    var checker = checkPeriod(start.value, end.value, toggleElement,alertText);
    checkObject[checkData] = checker;
    checkInput(checkObject,toggleElement);
    checkObject[checkData] === "" ? $("#"+start.id).addClass('is-invalid') : $("#"+start.id).removeClass('is-invalid');
    checkObject[checkData] === "" ? $("#"+end.id).addClass('is-invalid') : $("#"+end.id).removeClass('is-invalid');
}

function generatePassword(length) {
       var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}

function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}

/**
 * Returns a random integer between min (inclusive) and max (inclusive).
 * The value is no lower than min (or the next integer greater than min
 * if min isn't an integer) and no greater than max (or the next integer
 * lower than max if max isn't an integer).
 * Using Math.round() will give you a non-uniform distribution!
 */
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}


function getDates(startDate, daysToAdd) { 
  var aryDates = []; 
  for (var i = 0; i <= daysToAdd; i++) { 
   var currentDate = new Date(); 
    currentDate.setDate(startDate.getDate() + i); 
  /*  aryDates.push(DayAsString(currentDate.getDay()) + ", " + currentDate.getDate() + " " + MonthAsString(currentDate.getMonth()) + " " + currentDate.getFullYear());*/
    aryDates.push(
	{"day":DayAsString(currentDate.getDay()),
	 "date": currentDate.getDate(),
	 "month": MonthAsString(currentDate.getMonth()),
	 "year": currentDate.getFullYear()
	});
  } 
    return aryDates; 
} 

function rem(item,array){
  var index = array.indexOf(item); 
  if (index !== -1) { 
    array.splice(index, 1); 
  }
}
function MonthAsString(monthIndex) { 
  var d = new Date(); 
  var month = new Array(); 
      month[0] = "January";
      month[1] = "February"; 
      month[2] = "March"; 
      month[3] = "April"; 
      month[4] = "May"; 
      month[5] = "June"; 
      month[6] = "July"; 
      month[7] = "August"; 
      month[8] = "September"; 
      month[9] = "October"; 
      month[10] = "November"; 
      month[11] = "December"; 
  return month[monthIndex]; 
} 

function DayAsString(dayIndex) { 
  var weekdays = new Array(7); 
      weekdays[0] = "Sunday"; 
      weekdays[1] = "Monday"; 
      weekdays[2] = "Tuesday"; 
      weekdays[3] = "Wednesday"; 
      weekdays[4] = "Thursday"; 
      weekdays[5] = "Friday"; 
      weekdays[6] = "Saturday"; 
  return weekdays[dayIndex]; 
}

function basename(path) {
   return path.split('/').reverse()[0];
}

function removeImage(filePath){
  return $.ajax({
      type:"POST",
      headers: {
	'XSRF-TOKEN': $('meta[name="_token"]').attr('content'),
	'Accept': 'application/json',
      },
      url:"api/delete-file",
      data: {filePath: filePath},
      datatype: 'json',
      error: function(err){
	return err;
      },
      success:function(data){
	return data;
      }
  });
}

function getRandomInteger(min, max) {
  return Math.floor(Math.random() * (max - min) ) + min;
}

/*
 * Accepts:
 * color: the background color of the alert,
 * text: plain text in alert,
 * bold: bold text before text and inline with it,
 * textColor: color of all text nodes in alert defaults to white,
 */
function showAlert(color,text,bold,textColor){
  color = color; text = text; bold = bold; textColor = textColor;
  var colors = ['primary','secondary','success','danger'];
  for(var i=0;i<colors.length;i++){
    $("#alert").removeClass("alert-"+colors[i]);
  }
  bold =  (typeof bold !== 'undefined') ?  bold : '';
  textColor =  (typeof textColor !== 'undefined') ?  textColor : "white";

  $("#alert-bold").text(bold);
  $("#alert-bold").addClass("text-"+textColor);
  $("#alert-text").text(text);
  $("#alert-text").addClass("text-"+textColor);
  $("#alert").addClass("alert-"+color);
  $("#alert").show();
  pushDownNavbar();
	setTimeout(function(){hideAlert()},10000);
}

function show(){
    nowuiDashboard.showSidebarMessage('Sidebar mini deactivated...');
}
function hideAlert(){
 $("#alert").hide();
 pushUpNavbar();
}

function rankKey(key,fromZero){
  key = parseInt(key);
  fromZero===undefined ? true : fromZero;
  var nthKey = fromZero ? key+1 : key;
  switch(nthKey){
    case 1:
      return "first";
      break;
    case 2:
      return "second";
      break;
    case 3:
      return "third";
      break;
    default:
      return nthKey+"th";
  }
}

var assetsPath = "http://coin-bureau.herokuapp.com/";

function checkConnectionLoss() {
  $.ajax({url: assetsPath,
        type: "HEAD",
	async: false,
        statusCode: {
            0: function (response) { 
		showAlert("danger","Internet Connection lost");
            }
        }
     });
}

function checkPathEmpty(path){
 return $.ajax({
      type:"POST",
      headers: {
	'XSRF-TOKEN': $('meta[name="_token"]').attr('content'),
	'Accept': 'application/json',
      },
      url:"api/check-path-empty",
      data: {path: path, containsFile: true},
      datatype: 'json',
      error: function(err){
	checkConnectionLoss();
      },
      success:function(data){
	//return data;
      }
  });
}
function dollar(number){
  var formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',

  // These options are needed to round to whole numbers if that's what you want.
  //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
  //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
  });

 return formatter.format(number); 
}
function dollarToCoin(amount, coinDollarWorth){
  var equiv = Number(amount)/Number(coinDollarWorth);
  var res = equiv.toFixed(8);
  return res*1
}

function htmlToText(html){
    return html.replace(/<[^>]*>/g, '');
}

function initialWords(text, length){
    return text.split(' ').slice(0,length).join(' ');
}

function removeTag(html, tag){
    var div = document.createElement('div');
    div.innerHTML = html;

    // get all tag elements from div
    var elements = div.getElementsByTagName(tag);

    // remove all <a> elements
    while (elements[0])
	elements[0].parentNode.removeChild(elements[0])

    // get div's innerHTML into a new variable
    var repl = div.innerHTML;

    return repl
}

function emptyArray(array){
    array.length = 0;
}

function stringBefore(string, character){
    return string.split(character)[0];
}

function stringAfter(string, character){
    return string.split(character)[1];
}

function stringBetween(string, character1, character2){
    return stringAfter(stringBefore(string, character2),character1);
}
function getPrices(){
  var wtf = 1;
    $.ajax({
	  type:"GET",
	  url:"/prices",
	  headers: {
            'Accept': 'application/json',
	    'XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
	  },
	  error: function(err){
		  return err.responseJSON;
	    //showAlert('danger','Some error occured');
	  },
	  success:function(data){
		var neat = {
		    btc: data.bitcoin.usd,
		    bnb: data.binancecoin.usd,
		    doge: data.dogecoin.usd,
		    usdt: data.tether.usd,
		    ada: data.cardano.usd,
		    eth: data.ethereum.usd
		};
		  aJ($(this));
	   },
	});

}

function getCoinSvg(coin){
    return assetsPath + 'dashboard/vendors/cryptofont-1.3.0/SVG/' + coin + '.svg'
}

function trustLink(asset,address,amount){
    return "https://link.trustwallet.com/send?asset=c" + asset + "&address=" + address + "&amount=" + amount;
}

function qrLink(asset,address,amount){
    return "https://chart.googleapis.com/chart?chs=360x360&chld=L|2&cht=qr&chl=" + asset + ":" + address + "?amount=" + amount;
}

function showLoading(reg, load){
    reg.addClass('d-none');
    load.removeClass('d-none');
}

function hideLoading(reg, load){
    load.addClass('d-none');
    reg.removeClass('d-none');
}

function clearFields(){
    $(':input')
  .not(':button, :submit, :reset, :hidden')
  .val('')
  .prop('checked', false)
  .prop('selected', false);
}

function percentage(amount, percent){
    return (amount * percent)/100;
}

function handleCommonErrorsOld(error){
    var data = error.data;
    var message;
    if(error.message == "CSRF token mismatch."){
	location.reload(); return false;
    }else if(error.type === 'validation'){
	for(var key in error.data){
	    var info = error.data[key][0];
	    var k = key.substring(key.indexOf('.') + 1); 
	    message = data[Object.keys(data)[Object.keys(data).length - 1]];
	    if(key.includes("client")){
		message = capFirst(k) + " " + info.substring(info.indexOf('must'));
	    }else if(info == "The password format is invalid."){
		info = "Password must cointain at least: a letter, a number and a character(Ex. #, !, ?...)";
            }
	    invalidate($('#'+key),info);
	}
	message = data[Object.keys(data)[Object.keys(data).length - 1]];
	return Swal.fire({
	  icon: 'error',
  	  title: 'Error',
  	  text: message
	});
    }else {
	message = typeof(error.data) == 'string' ? error.data : 'Some error occurred. Please check internet connection';
	Swal.fire({ 
	    icon: 'error',
	    title: 'Error',
	    text: message
	});
    }
}

function handleCommonErrors(error){
  var data = error.data;
  var message;
  if(error.message == "CSRF token mismatch."){
location.reload(); return false;
  }else if(error.type === 'validation'){
for(var key in error.data){
    var info = error.data[key][0];
    var k = key.substring(key.indexOf('.') + 1); 
    message = data[Object.keys(data)[Object.keys(data).length - 1]];
    if(key.includes("client")){
  message = capFirst(k) + " " + info.substring(info.indexOf('must'));
    }else if(info == "The password format is invalid."){
  info = "Password must cointain at least: a letter, a number and a character(Ex. #, !, ?...)";
    }
    invalidate($('#'+key),info);
}
message = data[Object.keys(data)[Object.keys(data).length - 1]];
return Swal.fire({
    icon: 'error',
    title: 'Error',
    text: message
});
  }else if(!error.status){

return Swal.fire({
    icon: 'error',
    title: 'Error',
    text: error.data
});
  }else {
message = typeof(error.data) == 'string' ? error.data : 'Some error occurred. Please check internet connection';
Swal.fire({ 
    icon: 'error',
    title: 'Error',
    text: message
});
  }
}

function PrintElem(elem)
{
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}

function visible(inputField){
    var type = inputField.attr("type");
    if( type === 'password' ){
        inputField.attr("type", "text");
    }else{
        inputField.attr("type", "password");
    } 
}

