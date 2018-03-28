/*********** USER **********/
/* La partie de l'API qui touche aux users de la BDD */

class User {
  constructor() { }

  getAll() {
    console.log('GET /user');
    var query = 'SELECT * FROM user;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  get(body, res) {
    console.log("POST /get_user");
    var query = 'SELECT * FROM user WHERE email = \'' + body.email + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }
}

module.exports = User;
