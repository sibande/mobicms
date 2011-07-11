<?php
/**
 * Paginates data
 *
 * @package     mobicms
 * @subpackage  lib
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Lib_Paginator
{
  /**
   * __construct
   *
   * @param   array   data to paginate
   * @param   int     number of rows per page
   * @param   int     page number
   * @param   string  page uri
   * @return  void
   */
  public function __construct($data, $per_page, $page, $page_uri=NULL)
  {
    $this->data = $data;

    $this->per_page = $per_page;
    $this->total_pages = $this->_total_pages();
    
    $this->page = ($page > $this->total_pages) ? $this->total_pages :
      ((! $page < 1) ? $page : 1);
    $this->page_uri = $page_uri;
  }

  /**
   * Page data to display.
   *
   * @return  array
   */
  public function page_data()
  {
    return array_slice($this->data, ($this->per_page*($this->page-1)), $this->per_page);
  }

  /**
   * Calculates total pages that can be displayed.
   *
   * @return  float
   */
  public function _total_pages()
  {
    return ceil(count($this->data)/$this->per_page);
  }
  
  /**
   * Checks if there is next page.
   *
   * @return  bool
   */
  public function has_next()
  {
    return $this->page < $this->total_pages;
  }

  /**
   * Checks if there is a previous page.
   *
   * return bool
   */
  public function has_previous()
  {
    return $this->page > 1;
  }
  
  /**
   * Gets last page number.
   *
   * @return  int
   */
  public function last_page()
  {
    return $this->total_pages;
  }
  
  /**
   * Gets next page number.
   *
   * @return  int
   */
  public function next_page_number()
  {
    return $this->page+1;
  }

  /**
   * Gets previous page number.
   *
   * @return int
   */
  public function previous_page_number()
  {
    return $this->page-1;
  }
}