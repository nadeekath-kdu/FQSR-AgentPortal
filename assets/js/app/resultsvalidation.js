function validateEducationalResults() {
    // Validate A/L section
    if (!$('#examNameAL').val()?.trim()) {
        toastr.error("Please enter A/L examination name/school", '', { timeOut: 1000 });
        $('#examNameAL').focus();
        return false;
    }

    // Check if at least 3 A/L subjects are filled
    let alSubjectsCount = 0;

    // Get all A/L subject fields that exist in the form
    let alFields = $('[id^=subject_AL_]').map(function () {
        let index = this.id.split('_')[2];
        return {
            index: index,
            subject: $(`#subject_AL_${index}`),
            result: $(`#result_AL_${index}`),
            year: $(`#year_AL_${index}`)
        };
    }).get();

    //console.log('Found A/L fields:', alFields.map(f => f.index));

    // Use actual fields found instead of counter
    let alRowCount = alFields.length;

    // Check each found field set
    alFields.forEach(field => {
        //console.log('Checking A/L row:', field.index);

        let subjectVal = field.subject.val()?.trim();
        let resultVal = field.result.val()?.trim();
        let yearVal = field.year.val()?.trim();

        // Log values found
        /* console.log('Values found:', {
            subject: subjectVal,
            result: resultVal,
            year: yearVal
        }); */

        if (subjectVal && resultVal && yearVal) {
            alSubjectsCount++;
            //console.log('Valid complete entry found at index:', field.index);
        } else {
            console.log('Incomplete entry at index:', field.index);
        }
    });

    //console.log('A/L Valid subjects count:', alSubjectsCount);

    if (alSubjectsCount < 3) {
        toastr.error("Please enter at least 3 A/L subjects with results and years", '', { timeOut: 1000 });
        // Focus on the first empty required field
        for (const field of alFields) {
            //console.log('Checking A/L error at index:', field.index);

            if (!field.subject.val()?.trim()) {
                //console.log('Empty subject found at:', field.index);
                field.subject.focus();
                break;
            }

            if (!field.result.val()?.trim()) {
                //console.log('Empty result found at:', field.index);
                field.result.focus();
                break;
            }

            if (!field.year.val()?.trim()) {
                //console.log('Empty year found at:', field.index);
                field.year.focus();
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

    // Get all O/L subject fields that exist in the form
    let olFields = $('[id^=subject_OL_]').map(function () {
        let index = this.id.split('_')[2];
        return {
            index: index,
            subject: $(`#subject_OL_${index}`),
            result: $(`#result_OL_${index}`),
            year: $(`#year_OL_${index}`)
        };
    }).get();

    console.log('Found O/L fields:', olFields.map(f => f.index));

    // Use actual fields found instead of counter
    let olRowCount = olFields.length;

    // Check each found field set
    olFields.forEach(field => {
        //console.log('Checking O/L row:', field.index);

        let subjectVal = field.subject.val()?.trim();
        let resultVal = field.result.val()?.trim();
        let yearVal = field.year.val()?.trim();

        // Log values found
        /* console.log('Values found:', {
            subject: subjectVal,
            result: resultVal,
            year: yearVal
        }); */

        if (subjectVal && resultVal && yearVal) {
            olSubjectsCount++;
            //console.log('Valid complete entry found at index:', field.index);
        } else {
            console.log('Incomplete entry at index:', field.index);
        }
    });

    console.log('O/L Valid subjects count:', olSubjectsCount);

    if (olSubjectsCount < 3) {
        toastr.error("Please enter at least 3 O/L subjects with results and years", '', { timeOut: 1000 });
        // Focus on the first empty required field
        for (const field of olFields) {
            //console.log('Checking O/L error at index:', field.index);

            if (!field.subject.val()?.trim()) {
                //console.log('Empty subject found at:', field.index);
                field.subject.focus();
                break;
            }

            if (!field.result.val()?.trim()) {
                //console.log('Empty result found at:', field.index);
                field.result.focus();
                break;
            }

            if (!field.year.val()?.trim()) {
                //console.log('Empty year found at:', field.index);
                field.year.focus();
                break;
            }
        }
        return false;
    }

    return true;
}
