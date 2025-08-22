function addtoEducational(){    
    var curcnt = parseInt($('#edurowcnt').val());
    curcnt = curcnt + 1;    
    var addrow_markup = '<tr><td><input class="form-control" id="subject_AL_'+curcnt+'" type="text" name="subject_AL_'+curcnt+'"  placeholder="Enter Subject" /></td><td><input class="form-control" id="result_AL_'+curcnt+'"  name="result_AL_'+curcnt+'"  placeholder="Enter Result" /></td><td><input class="form-control" id="year_AL_'+curcnt+'"  name="year_AL_'+curcnt+'"  placeholder="Enter Year" /></td></tr>';
    $('#edutbl tr:last').after(addrow_markup);
    $('#edurowcnt').val(curcnt);
}

function remfromEducational(){
    var curcntdel = parseInt($('#edurowcnt').val());

    if(curcntdel > 0){
        $('#edutbl tr:last'). remove();
        curcntdel = curcntdel - 1;
        $('#edurowcnt').val(curcntdel);
    }
    //else{
        //alert('You should keep at least 3 rows');
    //}
}
function addtoEducational_ol(){    
    var curcnt = parseInt($('#edurowcnt2').val());
    curcnt = curcnt + 1;    
    var addrow_markup = '<tr><td><input class="form-control" id="subject_OL_'+curcnt+'" type="text" name="subject_OL_'+curcnt+'"  placeholder="Enter Subject" /></td><td><input class="form-control" id="result_OL_'+curcnt+'"  name="result_OL_'+curcnt+'"  placeholder="Enter Result" /></td><td><input class="form-control" id="year_OL_'+curcnt+'"  name="year_OL_'+curcnt+'"  placeholder="Enter Year" /></td></tr>';
    $('#edutbl2 tr:last').after(addrow_markup);
    $('#edurowcnt2').val(curcnt);
}

function remfromEducational_ol(){
    var curcntdel = parseInt($('#edurowcnt2').val());
    //alert(curcntdel);
    if(curcntdel > 0){
        $('#edutbl2 tr:last'). remove();
        curcntdel = curcntdel - 1;
        $('#edurowcnt2').val(curcntdel);
    }
    //else{
        //alert('You should keep at least 3 rows');
    //}
}

function addtoEnglish_qualification(){    
    var curcnt = parseInt($('#edurowcnt3').val());
    //alert(curcnt);
    curcnt = curcnt + 1;    
    var addrow_markup = '<tr><td><input class="form-control" id="name_EP_'+curcnt+'" type="text" name="name_EP_'+curcnt+'"  placeholder="Enter Qualification Type" /></td><td><input class="form-control" id="result_EP_'+curcnt+'"  name="result_EP_'+curcnt+'"  placeholder="Enter Result" /></td><td><input class="form-control" id="year_EP_'+curcnt+'"  name="year_EP_'+curcnt+'"  placeholder="Enter Year" /></td></tr>';
    $('#ep_tbl tr:last').after(addrow_markup);
    $('#edurowcnt3').val(curcnt);
}

function remfromEnglish_qualification(){
    var curcntdel = parseInt($('#edurowcnt3').val());
    //alert(curcntdel);
    if(curcntdel > 0){
        $('#ep_tbl tr:last'). remove();
        curcntdel = curcntdel - 1;
        $('#edurowcnt3').val(curcntdel);
    }
    //else{
        //alert('You should keep at least 3 rows');
    //}
}
function addtoProfessional(){    
    var curcnt = parseInt($('#profrowcnt').val());
    curcnt = curcnt + 1;    
    var addrow_markup = '<tr><th scope="row"><input class="form-control py-4" id="memberinstitution_'+curcnt+'" type="text" name="memberinstitution_'+curcnt+'"  placeholder="Institution name" /></th><td><input class="form-control py-4" id="membercat_'+curcnt+'" type="text" name="membercat_'+curcnt+'"  placeholder="Membership category" /></td><td><input class="form-control py-4" id="admityr_'+curcnt+'" type="number" min="1950" name="admityr_'+curcnt+'"  placeholder="Year of admission" /></td></tr>';
    $('#proftbl tr:last').after(addrow_markup);
    $('#profrowcnt').val(curcnt);
}

function remfromProf(){
    var curcntdel = parseInt($('#profrowcnt').val());

    if(curcntdel > 3){
        $('#proftbl tr:last'). remove();
        curcntdel = curcntdel - 1;
        $('#profrowcnt').val(curcntdel);
    }else{
        alert('You should keep at least 3 rows');
    }
}

function addtoEmployment(){    
    var curcnt = parseInt($('#employrowcnt').val());
    curcnt = curcnt + 1;    
    var addrow_markup = '<tr><th scope="row"><input class="form-control py-4" id="prevcompany_'+curcnt+'" type="text" name="prevcompany_'+curcnt+'"  placeholder="Company name" /></th><td><input class="form-control py-4" id="prevDesign_'+curcnt+'" type="text" name="prevDesign_'+curcnt+'"  placeholder="Enter desigination" /></td><td><input class="form-control py-4" id="prevDuration_'+curcnt+'" type="text" name="prevDuration_'+curcnt+'"  placeholder="Enter duration" /></td></tr>';
    $('#employtbl tr:last').after(addrow_markup);
    $('#employrowcnt').val(curcnt);
}

function remfromEmploy(){
    var curcntdel = parseInt($('#employrowcnt').val());

    if(curcntdel > 3){
        $('#employtbl tr:last'). remove();
        curcntdel = curcntdel - 1;
        $('#employrowcnt').val(curcntdel);
    }else{
        alert('You should keep at least 3 rows');
    }
}