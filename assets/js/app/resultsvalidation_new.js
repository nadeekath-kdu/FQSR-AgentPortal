function validateEducationalResults() {
    // Validate A/L section
    if (!$('#examNameAL').val()?.trim()) {
        toastr.error("Please enter A/L examination name/school", '', { timeOut: 1000 });
        $('#examNameAL').focus();
        return false;
    }

    // Check if at least 3 A/L subjects are filled
    let alSubjectsCount = 0;

    // Get actual number of visible rows for A/L
    let alRowCount = Math.max(
        parseInt($('#edurowcnt').val()) || 0,
        $('[id^=subject_AL_]').length  // Count actual subject fields
    );

    // Check if we're starting from index 0 or 1
    let alStartIndex = $('#subject_AL_0').length ? 0 : 1;
    console.log('A/L Starting index:', alStartIndex);
    console.log('A/L Row Count:', alRowCount);
    console.log('A/L Fields found:', $('[id^=subject_AL_]').map(function () { return this.id; }).get());

    for (let i = alStartIndex; i < (alStartIndex + alRowCount); i++) {
        // Check if all elements exist first
        if ($('#subject_AL_' + i).length &&
            $('#result_AL_' + i).length &&
            $('#year_AL_' + i).length) {

            let subjectVal = $('#subject_AL_' + i).val()?.trim();
            let resultVal = $('#result_AL_' + i).val()?.trim();
            let yearVal = $('#year_AL_' + i).val()?.trim();

            if (subjectVal && resultVal && yearVal) {
                alSubjectsCount++;
                console.log('Valid A/L entry found at index:', i);
            }
        }
    }

    console.log('A/L Valid subjects count:', alSubjectsCount);

    if (alSubjectsCount < 3) {
        toastr.error("Please enter at least 3 A/L subjects with results and years", '', { timeOut: 1000 });
        // Focus on the first empty required field
        for (let i = alStartIndex; i < (alStartIndex + alRowCount); i++) {
            if (!$('#subject_AL_' + i).val()?.trim()) {
                $('#subject_AL_' + i).focus();
                break;
            } else if (!$('#result_AL_' + i).val()?.trim()) {
                $('#result_AL_' + i).focus();
                break;
            } else if (!$('#year_AL_' + i).val()?.trim()) {
                $('#year_AL_' + i).focus();
                break;
            }
        }
        return false;
    }

    // Validate O/L section
    if (!$('#examNameOL').val()?.trim()) {
        toastr.error("Please enter O/L examination name/school", '', { timeOut: 1000 });
        $('#examNameOL').focus();
        return false;
    }

    // Check if at least 3 O/L subjects are filled
    let olSubjectsCount = 0;

    // Get actual number of visible rows for O/L
    let olRowCount = Math.max(
        parseInt($('#edurowcnt2').val()) || 0,
        $('[id^=subject_OL_]').length  // Count actual subject fields
    );

    // Check if we're starting from index 0 or 1
    let olStartIndex = $('#subject_OL_0').length ? 0 : 1;
    console.log('O/L Starting index:', olStartIndex);
    console.log('O/L Row Count:', olRowCount);
    console.log('O/L Fields found:', $('[id^=subject_OL_]').map(function () { return this.id; }).get());

    for (let i = olStartIndex; i < (olStartIndex + olRowCount); i++) {
        // Check if all elements exist first
        if ($('#subject_OL_' + i).length &&
            $('#result_OL_' + i).length &&
            $('#year_OL_' + i).length) {

            let subjectVal = $('#subject_OL_' + i).val()?.trim();
            let resultVal = $('#result_OL_' + i).val()?.trim();
            let yearVal = $('#year_OL_' + i).val()?.trim();

            if (subjectVal && resultVal && yearVal) {
                olSubjectsCount++;
                console.log('Valid O/L entry found at index:', i);
            }
        }
    }

    console.log('O/L Valid subjects count:', olSubjectsCount);

    if (olSubjectsCount < 3) {
        toastr.error("Please enter at least 3 O/L subjects with results and years", '', { timeOut: 1000 });
        // Focus on the first empty required field
        for (let i = olStartIndex; i < (olStartIndex + olRowCount); i++) {
            if (!$('#subject_OL_' + i).val()?.trim()) {
                $('#subject_OL_' + i).focus();
                break;
            } else if (!$('#result_OL_' + i).val()?.trim()) {
                $('#result_OL_' + i).focus();
                break;
            } else if (!$('#year_OL_' + i).val()?.trim()) {
                $('#year_OL_' + i).focus();
                break;
            }
        }
        return false;
    }

    return true;
}
