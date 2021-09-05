import { Component, OnInit, DoCheck } from '@angular/core';//PARA REFRESCAR AUTOMATICAMENTE
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from './services/user.service';
import { User } from './models/user';
import { global } from './services/global.service';
import { from } from 'rxjs';
import { RouterModule } from '@angular/router';
import { List } from './models/list';
import { ListService } from './services/list.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService, ListService]
})
export class AppComponent implements OnInit, DoCheck {
  public identity;
  public token;
  public url;
  public user: User;
  public avatar: string;
  public origin: string;
  public id;
  public status: string;
  public loading: boolean;
  public userComplete:boolean;
  public list:List;
  public lists;

  constructor(
    private _userService: UserService,
    private _listService: ListService,
    private _router: Router,
    private _route: ActivatedRoute,
  ) {
    this.url = global.url;
    this.list = new List(null,null,null);
    this.origin = this._userService.getOriginLogin();
    this.token      = this._userService.getToken();
    
  }

  ngOnInit() {
    this.loadUser();
  }

  ngDoCheck() {
    this.loadUser();
    this.origin = this._userService.getOriginLogin();
    if (this.origin == '{"userlogin"}') { this.origin = 'userlogin' };
  }

  loadUser() {
    this.user = this._userService.getIdentity();
    if (this.user != null) {
      this.avatar = this.user.image;
    }
  }


  coverDisable(){
    localStorage.removeItem('cover');
  }

  createList(form){
    this.loading=true;
    console.log(this.token, this.list, this.list.name);
    this._listService.create(this.token, this.list).subscribe(
      response => {
        if(response.status ='success'){
          this.status= 'success';
          form.reset();
          this.loading=false;
        }else{
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

}




