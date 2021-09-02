import { Component, OnInit, DoCheck } from '@angular/core';//PARA REFRESCAR AUTOMATICAMENTE
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from './services/user.service';
import { User } from './models/user';
import { global } from './services/global.service';
import { from } from 'rxjs';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService]
})
export class AppComponent implements OnInit, DoCheck {
  public title = 'blog-angular';
  public identity;
  public token;
  public url;
  public budguet;
  public row;
  public rows;
  public categories;
  public user: User;
  public avatar: string;
  public origin: string;
  public id;
  public budguetName;
  public budguetId;
  public rowQuantity;
  public status: string;
  public loading: boolean;
  public rows_budguet;
  public userComplete:boolean;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
  ) {
    this.url = global.url;
    this.origin = this._userService.getOriginLogin();
    
  }

  ngOnInit() {
    this.loadUser();
    this.budguet = JSON.parse(localStorage.getItem('budguet-detail'));
  }

  ngDoCheck() {
    this.loadUser();
    this.origin = this._userService.getOriginLogin();
    if (this.origin == '{"userlogin"}') { this.origin = 'userlogin' };

    this.rows_budguet = localStorage.getItem('row-' + this.budguetId + '-list');
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

}




