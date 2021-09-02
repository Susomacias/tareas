import {HttpClient, HttpHeaders} from '@angular/common/http';
import { Observable, Subscriber } from 'rxjs';
import { global } from './global.service';

class APIResponse<Model> {
    constructor (
        public status: string,
        public hash: string,
        public list: Array<Model>
    ){}
}

export class ServiceBase<Model> {
    protected keyHash : string;
    protected keyValues : string;
    public url: string;
    public values : Array<Model>;
    public hash: string;
    public listObserver : Observable<Array<Model>>;
    public listSuscriber : Subscriber<Array<Model>>;
    public busyObserver : Observable<boolean>;
    public busySuscriber : Subscriber<boolean>;
    
 
    constructor(
        protected _http: HttpClient,
        _key: string
    ){
        this.url = global.url + _key + '/';
        this.keyHash = _key + '-hash';
        this.keyValues = _key + '-list';
        this.init();
        this.listObserver = new Observable<Array<Model>>((suscriber) => {
            this.listSuscriber = suscriber;
        });
        this.busyObserver = new Observable<boolean>((suscriber) => {
            this.busySuscriber = suscriber;
        });
    }

    protected init() {
        this.hash = localStorage.getItem(this.keyHash);
        try { // Obtenemos los valores de localStorage
            this.values = JSON.parse(localStorage.getItem(this.keyValues));
        } catch(ex) { // Si los datos están corruptos, los eliminamos
            localStorage.removeItem(this.keyHash);
            localStorage.removeItem(this.keyValues);
        }
    }

    create(token, data): Observable<any>{
        let json = JSON.stringify(data);
        let params = new FormData();
        params.append('json', json);

        let headers = new HttpHeaders().set('Authorization', token);
        
        return this._http.post(this.url + 'create', params, {headers: headers});
    } 
    

    public getList(token, identity): Observable<Array<Model>> {
        if(this.busySuscriber) {
            this.busySuscriber.next(true);
        }
        if(this.values != null) {
            this.next();
        }
        this.refresh(token, identity);
        return this.listObserver;
    }

    refresh(token, identity) : void {
        let json = JSON.stringify(identity);
        let params = new FormData();
        params.append('json', json);

        let headers = new HttpHeaders().set('Authorization', token);

        this._http.post(this.url + 'list', params, {headers: headers})
            .subscribe(response => {
                this.save(response as APIResponse<Model>);
            }, error => { this.listSuscriber.error(error); });
    }

    update(token, data): void {
        if(this.busySuscriber) {
            this.busySuscriber.next(true);
        }
        let json = JSON.stringify(data);
        let params = new FormData();
        params.append('json', json);

        let headers = new HttpHeaders().set('Authorization', token);
        
        this._http.post(this.url + 'update', params, {headers: headers})
            .subscribe(response => {
                this.save(response as APIResponse<Model>);
            }, error => { this.listSuscriber.error(error); });
    }

    delete(token, data): void{
        if(this.busySuscriber) {
            this.busySuscriber.next(true);
        }
        let json = JSON.stringify(data);
        let params = new FormData();
        params.append('json', json);

        let headers = new HttpHeaders().set('Authorization', token);
        
        this._http.post(this.url + 'delete', params, {headers: headers})
            .subscribe(response => {
                this.save(response as APIResponse<Model>);
            }, error => { this.listSuscriber.error(error); });
    }

    isBusy() : Observable<boolean> {
        return this.busyObserver;
    }

    // Procesamos la respuesta de la lista de elementos
    private save(response: APIResponse<Model>) {
        if(this.busySuscriber) {
            this.busySuscriber.next(false);
        }
        if(response.status == 'success') {
            if(this.hash != response.hash) { // Si el hash ha cambiado almacenamos los valores en cache
                this.hash = response.hash;
                this.values = response.list;
                localStorage.setItem(this.keyHash, this.hash);
                localStorage.setItem(this.keyValues, JSON.stringify(this.values));
            } 
            this.next(); // Enviamos los valores al cliente
        } else { // En caso de error, replicamos el error
            this.listSuscriber.error(response);
        }
    }

    private next() {
        if(this.listSuscriber == null) {
            setTimeout(this.next.bind(this), 50);
        } else {
            this.listSuscriber.next(this.values);
        }
    }
      
}