// Server URL Configuration
var CONFIG = {
    BASE_URL: window.location.origin + '/FQSR',
    API_PATHS: {
        GET_ACADEMIC_YEAR: '/data/get_academic_year.php',
        GET_DEGREE_LIST: '/data/get_degree_list.php'
        // Add more API endpoints here
    },
    getApiUrl: function (apiPath) {
        return this.BASE_URL + apiPath;
    }
};
