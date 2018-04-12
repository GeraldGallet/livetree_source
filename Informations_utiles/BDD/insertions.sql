USE `projet_LiveTree`;
insert into status(id_status,rights)
values('teacher',1);

insert into user(email,password,phone_number,first_name,last_name,activated,id_status)
values ('aa@htt.fr','abcdef',09876543212,'jc','sim',true,'teacher');

insert into facility(name,complementary,address)
values ('ISEN','lille','quartier vauband'),
('HEI','lille','tool');

insert into facility(id_facility,name,address,)
values (1,'ISEN','lille','quartier vauband');
