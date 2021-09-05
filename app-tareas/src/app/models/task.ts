export class Task{
    constructor (
        public id: number,
        public user_id: number,
        public list_id: number,
        public name: string,
        public description: string,
        public color: string,
        public date: string,
    ){}
}