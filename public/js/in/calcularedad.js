function calculaEdad(fecha,fecha_nac){
    var a = moment(fecha);
    var b = moment(fecha_nac);

    var years = a.diff(b, 'year');
    b.add(years, 'years');

    var months = a.diff(b, 'months');
    b.add(months, 'months');

    var days = a.diff(b, 'days');
    b.add(days, 'days');

    if (years==0) {
       if (months<=1) {
          if (days<=1) {
	         return(months + ' mes ' + days + ' día');				
	      } else {
 	         return(months + ' mes ' + days + ' días');
	      }
       } else {
	      if (days<=1) {
	         return( months + ' meses ' + days + ' día');
	      } else {
	         return( months + ' meses ' + days + ' días');
	      }  
       }
    } else {
		if (years==1) {
			//return( years + ' año ' + months + ' mes ' + days + ' dia');


			if (months<=1) {
               if (days<=1) {
	              return(years + ' año ' + months + ' mes ' + days + ' día');				
	           } else {
 	              return(years + ' año ' + months + ' mes ' + days + ' días');
	           }
            } else {
	           if (days<=1) {
	              return( years + ' año ' + months + ' meses ' + days + ' día');
	           } else {
	              return( years + ' año ' + months + ' meses ' + days + ' días');
	           }  
            }

	    } else {
			//return( years + ' años ' + months + ' mes ' + days + ' dia');

			if (months<=1) {
               if (days<=1) {
	              return(years + ' años ' + months + ' mes ' + days + ' día');				
	           } else {
 	              return(years + ' años ' + months + ' mes ' + days + ' días');
	           }
            } else {
	           if (days<=1) {
	              return( years + ' años ' + months + ' meses ' + days + ' día');
	           } else {
	              return( years + ' años ' + months + ' meses ' + days + ' días');
	           }  
            }			
	    }	
	} 
}
