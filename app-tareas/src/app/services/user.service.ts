import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import { Observable } from 'rxjs';
import { User} from '../models/user';
import { Password} from '../models/password';
import { global } from './global.service';
import { SocialUser } from "angularx-social-login";

 
 
@Injectable()
export class UserService{
    public url: string;
    public identity;
    public origin;
    public token;
    providedIn: 'root';
    
    constructor(
        public _http: HttpClient
    ){
        this.url = global.url;

    }

    test(){
        return "Servicio usuario lanzado";
    }
 
    getUsers(): Observable<any>{
        return this._http.get(this.url+'user');
    }
 
    register(user): Observable<any>{
        let json = JSON.stringify(user);
        let params = 'json='+json;
        
        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded');
         
        return this._http.post(this.url+'register', params, {headers: headers});
    } 

    signup(user, captcha, gettoken = null): Observable<any>{
        if(gettoken !=null){
            user.gettoken = 'true';
        }
        let json = JSON.stringify(user);
        const formData: FormData = new FormData();
        formData.append('json', json);
        formData.append('captcha', captcha);
        //let params = 'json='+json;
        return this._http.post(this.url+'login', formData);
    }
////PASAR UN TOKEN

    getIdentity(){
        let identity = JSON.parse(localStorage.getItem('identity'));

        if(identity && identity != "undefined") {
            this.identity = identity;
        } else {
            this.identity = null;
        }
        return this.identity;
    }

    getOriginLogin(){
        let origin = localStorage.getItem('origin');

        if(origin && origin != "undefined") {
            this.origin = origin;
        } else {
            this.origin = null;
        }
        return this.origin;
    }

    getIdentityimage(){
        let identity = JSON.parse(localStorage.getItem('identity'));

        if(identity && identity.image != "undefined") {
            this.identity = identity.image;
        } else {
            this.identity.image = null;
        }
        return this.identity.image;
    }

    getToken(){
        let token = localStorage.getItem('token');

        if(token && token !="undefined"){
            this.token = token;
        }else{
            this.token=null;
        }
        return this.token;
    }


    update(token, user): Observable<any>{
        let json = JSON.stringify(user);
        let params = "json="+json;

        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded')
                                        .set('Authorization', token);
        
        return this._http.put(this.url+'user/update', params, {headers: headers});
    }

    getPosts(id):Observable<any>{
        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded');
        return this._http.get(this.url+'post/user/'+id, {headers: headers});
    }

    getUser(id):Observable<any>{
        return this._http.get(this.url+'user/detail/' + id);
    }

    getImage(user):Observable<any>{
        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded');
        return this._http.get(user.image, {headers: headers});
    }

    public postFile(fileToUpload: File, token) : Observable<any> {
        let headers     = new HttpHeaders().set('Authorization', token);
        const endpoint  = this.url + 'user/upload';
        const formData: FormData = new FormData();
        formData.append('file0', fileToUpload);
        return this._http.post(endpoint, formData, {headers: headers});
      }

    public postUser(fileToUpload: File, token, data) : Observable<any> {
        let headers     = new HttpHeaders().set('Authorization', token);
        const endpoint  = this.url + 'user/upload';
        const formData: FormData = new FormData();
        formData.append('file0', fileToUpload);
        formData.append('json',  JSON.stringify(data));
        return this._http.post(endpoint, formData, {headers: headers});
    }

      public get(url:string){
        return this._http.get(url); // GET  
      }
    
      public post(url:string, body){
        return this._http.post(url,body); // POST  
      }

      password(token, user): Observable<any>{
        let json = JSON.stringify(user);
        let params = "json="+json;

        let headers = new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded')
                                        .set('Authorization', token);
        
        return this._http.put(this.url+'user/password', params, {headers: headers});
    }


    passwordrecoveri(user): Observable<any>{
        let json = JSON.stringify(user);
        const formData: FormData = new FormData();
        formData.append('json', json);
        return this._http.post(this.url+'mail/passwordrecoveri', formData);
    }

    googlesignup(google_user): Observable<any>{
        let idToken = JSON.stringify(google_user.idToken);
       // let email = JSON.stringify(google_user.email);
        const formData: FormData = new FormData();
        formData.append('idtoken', idToken);
       // formData.append('email', email);
        //let params = 'json='+json;
        return this._http.post(this.url+'googlelogin', formData);
    }
}
