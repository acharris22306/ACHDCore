
function arraysEqual( arrayA, arrayB ) {
  return arrayA.length === arrayB.length && arrayA.every( function(i,j) { 
  		return i === arrayB[j]; 
	});  
}

function showFormElementDynamicaly(elem,selectedIndexNum,itemIdToShow) {
	if(elem.selectedIndex === selectedIndexNum){
  	  document.getElementById(itemIdToShow).style.display = 'block';
    }else{
   	 document.getElementById(itemIdToShow).style.display = 'none';
    }
}

function toggleVisibility(elem,itemIdToShow,itemIdToHide) {
	if(elem.checked === true){
  	  document.getElementById(itemIdToShow).style.display = 'block';
  	  document.getElementById(itemIdToHide).style.display = 'none';

    }else{
  	  document.getElementById(itemIdToShow).style.display = 'none';
  	  document.getElementById(itemIdToHide).style.display = 'block';

    }
}

function toggleVisibilityBinary(elem,itemIdToShow,doShow) {
	if (doShow === true){
		if(elem.checked === true){
			document.getElementById(itemIdToShow).style.display = 'block';
		}else{
			document.getElementById(itemIdToShow).style.display = 'none';
		}
	}else{
		if(elem.checked === true){
			document.getElementById(itemIdToShow).style.display = 'none';
		}else{
			document.getElementById(itemIdToShow).style.display = 'block';
		}
	}
}

function getXHRObj(){
	var xmlhttp;
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}