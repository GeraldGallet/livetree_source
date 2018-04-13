/*********** PERSONALCAR **********/
/* Part that touches the personal cars of the users */

class PersonalCar {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM company_car WHERE id_facility = \'" + body.id_facility + "\';";
    //console.log(query)
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get(body, res) {
    var query = "SELECT * FROM company_car WHERE id_facility = \'" + body.id_company + "\' AND name = \'" + body.name + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get_by_id(body, res) {
    var query = "SELECT * FROM company_car WHERE id_company_car = \'" + body.id_company_car + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    res.locals.connection.query('INSERT INTO company_car SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  delete(body, res) {
    var query = 'DELETE FROM company_car WHERE id_company_car = \'' + body.id_company_car + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = PersonalCar;
