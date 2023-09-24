<?php 
namespace Drupal\bne_resources\Controller;
use Symfony\Component\CssSelector\CssSelector;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Render\Element;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\views\Views;


class BneResourcesController extends ControllerBase {
    function bne_resources_subject_ajax_callback(array &$form, FormStateInterface $form_state) {
      // Obtiene el valor seleccionado en field_subject_resources.
      $materia_tid = $form_state->getValue('field_subject_resources')[0]['target_id'];
      // Llama a la función obtener_taxonomias_relacionadas con el valor seleccionado.
      $saberes_basicos_tids = obtener_taxonomias_relacionadas($materia_tid);
      // Actualiza dinámicamente el campo field_basic_knowledge.
      $form['field_basic_knowledge']['widget'][0]['#default_value'] = $saberes_basicos_tids;
      // Reemplaza el HTML del campo field_basic_knowledge en el formulario.
      $response = new AjaxResponse();
      $response->addCommand(new HtmlCommand(
        '#edit-field-basic-knowledge-wrapper',
        render($form['field_basic_knowledge'])
      ));
      return $response;
    }
  
    public function updateTaxonomy($tids) {
        try {
          // Convierte los TIDs de cadena a un array.
          $tidsArray = explode(',', $tids);
          // Llama a la función obtener_taxonomias_relacionadas con los TID seleccionados.
          $result = $this->obtenerTaxonomiasRelacionadas($tidsArray);
          // Devuelve la respuesta en formato JSON.
          return new JsonResponse($result);
        } catch (\Exception $e) {
          // Manejo de errores: registra el error y devuelve una respuesta de error si es necesario.
          \Drupal::logger('bne_resources')->error('Error en updateTaxonomy: @message', ['@message' => $e->getMessage()]);
          return new JsonResponse(['error' => 'Ha ocurrido un error en la solicitud AJAX.']);
        }
      }

    
    protected function obtenerTaxonomiasRelacionadas($materiaTids) {
        try {
            $saberesBasicosTids = [];
            // Consulta la taxonomía "Saberes Básicos" para encontrar términos relacionados.
            $query = \Drupal::entityQuery('taxonomy_term');
            $query->condition('vid', 'basic_knowledge'); // Reemplaza 'basic_knowledge' con el nombre de la vid de tu taxonomía.
            $query->condition('field_subject_resources', $materiaTids, 'IN');
            $tids = $query->execute();
            if (!empty($tids)) {
              $saberesBasicosTids = array_values($tids);
            }
            return $saberesBasicosTids;
        } catch (\Exception $e) {
          // Manejo de errores, puedes registrar el error o devolver un mensaje de error personalizado.
          \Drupal::logger('bne_resources')->error('Error en obtenerTaxonomiasRelacionadas: @message', ['@message' => $e->getMessage()]);
          return []; // Opcionalmente, puedes devolver una respuesta vacía en caso de error.
        }
      }
  
  }