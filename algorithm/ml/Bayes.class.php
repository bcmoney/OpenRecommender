<?php

/**
* Bayes
*
* Calculates posterior probabilities for m hypotheses and n evidence 
* alternatives.  The code was inspired by a procedural TrueBasic version 
* (Bayes.tru) bundled with Grimstead and Snell's excellent online 
* textbook "Introduction to Probability".
*/
class Bayes {

  /**
  * Number of evidence alternatives (that is, number of rows).
  */
  var $m;

  /**
  * Number of hypothesis alternatives (that is, number of columns).
  */
  var $n;

  /**
  * Output labels for evidence alternatives.
  */
  var $row_labels = array();
  
  /**
  * Output labels for hypothesis alternatives.
  */  
  var $column_labels = array();

  /**
  * Vector container for prior probabilities.
  */
  var $priors = array();

  /**
  * Matrix container for likelihood of evidence e given hypothesis h.
  */
  var $likelihoods = array();

  /**
  * Matrix container for posterior probabilties.
  */
  var $posterior = array();

  /**
  * Vector container for evidence probabilties.
  */
  var $evidence = array();

  /**
  * Initialize the Bayes algorithm by setting the priors, likelihoods 
  * and dimensions of the likelihood and posterior matrices.
  */
  function Bayes($priors, $likelihoods) {
    $this->priors = $priors;
    $this->likelihoods = $likelihoods;
    $this->m = count($this->likelihoods);  // num rows
    $this->n = count($this->likelihoods[0]); // num cols
    return true;
  }
  
  /**
  * Output method for setting row labels prior to display.
  */
  function setRowLabels($row_labels) {
    $this->row_labels = $row_labels;
    return true;
  }

  /**
  * Output method for setting column labels prior to display.
  */
  function setColumnLabels($column_labels) {
    $this->column_labels = $column_labels;
    return true;
  }

  /**
  * Compute the posterior probability matrix given the priors and 
  * likelihoods.
  *
  * The first set of loops computes the denominator of the canonical 
  * Bayes equation. The probability appearing in the denominator 
  * serves a normalizing role in the computation - it ensures that 
  * posterior probabilities sum to 1.
  *
  * The second set of loops:
  *
  *   1. multiplies the prior[$h] by the likelihood[$h][$e]
  *   2. divides the result by the denominator
  *   3. assigns the result to the posterior[$e][$h] probability matrix
  */
  function getPosterior() {
    // Find probability of evidence e
    for($e=0; $e < $this->n; $e++) {
      for ($h=0; $h < $this->m; $h++) {
        $this->evidence[$e] += $this->priors[$h]
           * $this->likelihoods[$h][$e];
      }
    }
    // Find probability of hypothesis given evidence
    for($e=0; $e < $this->n; $e++) {
      for ($h=0; $h < $this->m; $h++) {
        $this->posterior[$e][$h] = $this->priors[$h
           * $this->likelihoods[$h][$e] / $this->evidence[$e];
      }
    }
    return true;
  }
  
  /**
  * Output method for displaying posterior probability matrix
  */
  function toHTML($number_format="%01.3f") {
    ?>
    <table border='1' cellpadding='5' cellspacing='0'>
      <tr>
        <td> </td>
        <?php
        for ($h=0; $h < $this->m; $h++) {
          ?>
          <td align='center'>
             <b><?php echo $this->column_labels[$h] ?></b>
          </td>
          <?php
        }
        ?>
      </tr>
      <?php
      for($e=0; $e < $this->n; $e++) {
        ?>
        <tr>
          <td><b><?php echo $this->row_labels[$e] ?></b></td>
          <?php
          for ($h=0; $h < $this->m; $h++) {
            ?>
            <td align='right'>
               <?php printf($number_format, $this->posterior[$e][$h]) ?>
            </td>
            <?php
          }
          ?>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  }
}
?>