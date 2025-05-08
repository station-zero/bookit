function userProfile() {
    let jwt_hash = "";
    let login_status = false;
    let CalendarID = null;	
    let route = "";
    let receiver = null;
    let id = null;
    
    this.setJWT = (hash) => {jwt_hash = hash;}
    this.getJWT = () => jwt_hash;

    this.setLoginStatus = (bool) => {login_status = bool;}
    this.getLoginStatus = () => login_status;

    this.setCalendarID = (ID) => {CalendarID = ID;}
    this.getCalendarID = () => CalendarID;

    this.setCalendarUsers = (users) => {calendarUsers = users;}
    this.getCalendarUsers = () => calendarUsers;

    this.setRoute = (url) => {route = url;}
    this.getRoute = () => route;

    this.setReceiver = (id) => {receiver = id;}
    this.getReceiver = () => receiver;

    this.setID = (user_id) => {id = user_id;}
    this.getID = () => id;
}
