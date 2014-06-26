<?php

  // Connection settings
  $host = '127.0.0.1';
  $port = 9306;
  
  /**
   * Bootstrapping and quering
   */
  new DB($host, $port);

  if (isset($_POST['query']))
    $data = DB::query($_POST['query']);

  $indices = DB::query('SHOW TABLES');
  $data = DB::query('SELECT * FROM rtvideo LIMIT 10');

  /**
   * Query class
   */
  class DB
  {
    public static $connection;

    public function __construct($host = '127.0.0.1', $port = 9306)
    {
      self::$connection = new \MySQLi($host, null, null, null, $port, null);
    }

    public static function query($q)
    {
      $data = self::$connection->query($q);
      
      if ($data)
      {
        $data = $data->fetch_all(MYSQLI_ASSOC);
        array_walk($data, function(&$v){$v = (object)$v; });
      }

      return $data ?: false;
    }

  }


?>
<!-- Template view -->
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sphinx Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style type="text/css">
      
      body {
        padding-top: 50px;
      }

    </style>
  </head>
  <body>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Sphinx viewer</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="http://sphinxsearch.com/docs/">Documentation</a></li>
              <li><a href="#">Help</a></li>
            </ul>

          </div>
        </div>
    </div>

    <div class="container-fluid">
      
      <div class="row">

        <div class="col-lg-2 sidebar">
          <h6>Indexes</h6>
          <?php if ($indices):?>
            <ul class="nav nav-sidebar">
              <?php foreach ($indices as $i): ?>
                <li><a href="#"><?php echo $i->Index?> (<?=$i->Type?>)</a></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="col-lg-10 main">
          <div class="row">
            
            <div class="col-lg-12 main">
              <h6 class="sub-header">Query</h6>
                    
              <form>
              <div class="col-lg-10">
                  <input  type="text" value="" class="form-control">
              </div>
              <div class="col-lg-2">
                  <a href="#fakelink" class="btn btn-block btn-default btn-success">Go</a>
              </div>
              </form>  
            </div>

          </div>

            <h6 class="sub-header">Rows</h6>
            <div class="tile">
              Total rows found: <?php echo isset($data) ? count($data) : 0;?>
            </div>

            <div class="table-responsive">
            <?php if ($data): ?>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <?php $fields = reset($data); ?>
                    <?php foreach ($fields as $n => $f): ?>
                      <th><?=$n?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php $c = 1; foreach ($data as $value): ?>
                     <tr>
                      <td><?=$c++?></td>
                      <?php foreach ($value as $k => $v): ?>
                        <td><?=$v?></td>
                      <?php endforeach; ?>
                    </tr> 
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

    <!-- Bootstrap core JavaScript -->
    <!-- ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://raw.githubusercontent.com/designmodo/Flat-UI/master/css/flat-ui.css">

    <!-- Optional theme -->
    <!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"> -->

    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </body>
  </html>