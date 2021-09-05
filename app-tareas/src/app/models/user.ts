export class User{
    constructor (
        public id: number,
        public name: string,
        public email: string,
        public password: string,
        public image: string,
        public tasks_list: string,
        public newpassword: string,
    ){}
}