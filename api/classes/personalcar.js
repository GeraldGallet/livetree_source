/*********** PERSONALCAR **********/
/* Part that touches the personal cars of the users */

class PersonalCar {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM personal_car WHERE id_user = \'" + body.id_user + "\';";
    //console.log(query)
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get(body, res) {
    var query = "SELECT * FROM personal_car WHERE id_user = \'" + body.id_user + "\' AND name = \'" + body.name + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    res.locals.connection.query('INSERT INTO personal_car SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  remove(body, res) {

  }

}

module.exports = PersonalCar;
