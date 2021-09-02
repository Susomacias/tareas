export class User{
    constructor (
        public id: number,
        public name: string,
        public email: string,
        public password: string,
        public image: string,
        public newpassword: string,
        public address:string,
        public phone:string,
        public web:string,
        public taxes:number,
        public measure:string,
        public coin:string,
    ){}
}