import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params} from '@angular/router';
import {ContactService} from '../../../services/contact.service';
import {Contact} from '../../../models/contact';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.css'],
  providers: [ContactService],
})
export class ContactComponent implements OnInit {
  public page_title: string;
  public contact: Contact;
  public status: string;


  constructor(
    private _route: ActivatedRoute,
    private _router: Router,
    private _contactService: ContactService,
  ) { 
    this.page_title = "Contacta con nosotros";
    this.contact = new Contact('','','');
  }

  ngOnInit(): void {
  }

  onSubmit(form){
    this._contactService.create(this.contact).subscribe(
      response => {
        if(response.status ='success'){
          this.contact=response.contact;
          this.status= 'success';
          form.reset();
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

