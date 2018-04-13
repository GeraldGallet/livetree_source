/*********** STATE **********/
/* Part that touches the bornes */

class State {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM state;";
    //console.log(query)
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get_by_id(body, res) {
    var query = "SELECT * FROM state WHERE id_state = \'" + body.id_state + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get_by_resa(body, res) {
    var query = "SELECT * FROM state WHERE id_resa = \'" + body.id_resa + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    res.locals.connection.query('INSERT INTO state SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  delete(body, res) {
    var query = 'DELETE FROM state WHERE id_state = \'' + body.id_state + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = State;
