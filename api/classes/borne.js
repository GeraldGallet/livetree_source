/*********** PERSONALCAR **********/
/* Part that touches the bornes */

class Borne {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM borne WHERE id_place = \'" + body.id_place + "\';";
    //console.log(query)
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get(body, res) {
    var query = "SELECT * FROM borne WHERE id_borne = \'" + body.id_borne + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    res.locals.connection.query('INSERT INTO borne SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  delete(body, res) {
    var query = 'DELETE FROM borne WHERE id_borne = \'' + body.id_borne + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = Borne;
