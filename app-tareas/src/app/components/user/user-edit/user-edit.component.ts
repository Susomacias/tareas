import { Component, OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { from } from 'rxjs';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../../models/user';
import { UserService } from '../../../services/user.service';
import { global } from '../../../services/global.service';

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {

  public page_title: string;
  public codigo: number;
  public user: User;
  public status: string;
  public token;
  public identity;
  public origin;
  public url;
  public previsualizacion: string;
  public archivos: any = [];
  public loading: boolean;
  public avatar: string;
  public googlelogin: boolean;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
    private sanitizer: DomSanitizer,
    private rest: UserService,
  ) {
    this.page_title = 'Datos de usuario';
    this.token      = this._userService.getToken();
    this.user=new User(1, '','','','','','');

     //RELLENAR CAMPOS DE USUARIO CON DATOS DE USUARIO REGISTRADO
     this.identity = this._userService.getIdentity();
     this.origin = this._userService.getOriginLogin();
     this.url = global.url;
     
     this.user = new User(
       this.identity.sub,
       this.identity.name,
       this.identity.email, '',
       this.identity.image,
       this.identity.task_list,
       this.identity.newpassword,
     );
   }

  ngOnInit() {
    this.loadUser();
    //Si loguea con google redirije a Home.
    if (this.origin=='{"googlelogin"}'){this.googlelogin=true}else{this.googlelogin=false}
  }
  capturarFile(event): any {
    const archivoCapturado = event.target.files[0]
    this.extraerBase64(archivoCapturado).then((imagen: any) => {
      this.previsualizacion = imagen.base;
    })
    this.archivos.push(archivoCapturado)
  }


  extraerBase64 = async ($event: any) => new Promise((resolve, reject) => {
    try {
      const unsafeImg = window.URL.createObjectURL($event);
      const image = this.sanitizer.bypassSecurityTrustUrl(unsafeImg);
      const reader = new FileReader();
      reader.readAsDataURL($event);
      reader.onload = () => {
        resolve({
          base: reader.result
        });
      };
      reader.onerror = error => {
        resolve({
          base: null
        });
      };

    } catch (e) {
      return null;
    }
  })


  clearImage(): any {
    this.previsualizacion = '';
    this.archivos = [];
  }


  onSubmit(): any {
    try {
      this.loading = true;
      this.rest.postFile(this.archivos[0], this.token)
        .subscribe(data => {
          this.loading = false;
          if(data.status == 'success') {
            localStorage.setItem('identity', JSON.stringify(data.user));
          }
        }, () => {
          this.loading = false;
          alert('Error');
        })
    } catch (e) {
      this.loading = false;
      console.log('ERROR', e);
    }

  }

  userUpdate(form){
    this._userService.update(this.token, this.user).subscribe(
      response=> {
          if (response && response.status){
            this.status = 'success';

            if(response.change.name){
              this.user.name = response.change.name;
            }
            if(response.change.email){
              this.user.email = response.change.email;
            }
            this.identity = this.user;
            localStorage.setItem('identity', JSON.stringify(this.identity));

          }else{
            this.status = 'error';
          }
      },
      error => {
        this.status='error';
        console.log(<any>error);
      }
    );
  }

  loadUser(){
    this.user = this._userService.getIdentity();
    if(this.user != null) {
      this.avatar = this.user.image;
      console.log(this.user.image);
    }
  }
}


